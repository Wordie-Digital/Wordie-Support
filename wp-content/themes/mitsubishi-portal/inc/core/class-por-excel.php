<?php

defined( 'ABSPATH' ) or exit;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class POR_Excel {
  public function __construct() {
  }

  /**
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   * @throws \PhpOffice\PhpSpreadsheet\Exception
   */
  function generate_parts_list_spreadsheet( $material_id ): void {
    $inputFileName = get_theme_file_path( 'assets/files/bom-template.xlsx' );
    $material_post = get_post( $material_id );

    // Load $inputFileName to a Spreadsheet Object
    $spreadsheet     = IOFactory::load( $inputFileName );
    $activeWorksheet = $spreadsheet->getActiveSheet();

    // Add title
    $activeWorksheet->setCellValue( 'A4', $material_post->post_title . '    ' . $material_post->post_content );

    // Add rows
    $spare_parts = POR_Core::instance()->helpers->get_spare_parts_compatible_models( $material_post->post_title );
    $start_row   = 7;

    foreach ( $spare_parts as $index => $spare_part ) {
      $row_index = $start_row + $index;

      $activeWorksheet->getCell( 'A' . $row_index )->setValueExplicit( $spare_part['partNumber'], DataType::TYPE_STRING2 );
      $activeWorksheet->setCellValue( 'B' . $row_index, $spare_part['partDescription'] );
      $activeWorksheet->setCellValue( 'C' . $row_index, $spare_part['partQuantity'] );
    }

    $writer = new Xlsx( $spreadsheet );
    header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
    header( 'Content-Disposition: attachment; filename="' . sprintf( 'parts-list--%2$s--%1$s.xlsx', wp_date( 'dmY' ), $material_post->post_name ) . '"' );
    $writer->save( 'php://output' );
  }
}
