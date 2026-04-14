<?php

defined( 'ABSPATH' ) or exit;

class POR_Pdf {
  public function __construct() {
  }

  function generate_parts_list_pdf( $material_id ): void {
    /* @var WP_Post $pdf_post */
    global $pdf_post;

    if ( ! ( $pdf_post = get_post( $material_id ) ) ) {
      return;
    }

    // create new PDF document
    $pdf = new Mitsubishi_PDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );

    // set document information
    $pdf->SetCreator( PDF_CREATOR );
    $pdf->SetAuthor( 'Nicola Asuni' );
    $pdf->SetTitle( 'TCPDF Example 048' );
    $pdf->SetSubject( 'TCPDF Tutorial' );
    $pdf->SetKeywords( 'TCPDF, PDF, example, test, guide' );

    // set default header data
    $pdf->SetHeaderData( PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING );

    // set header and footer fonts
    $pdf->setHeaderFont( array( PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
    $pdf->setFooterFont( array( PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA ) );

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

    // set margins
    $pdf->SetMargins( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
    $pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
    $pdf->SetFooterMargin( PDF_MARGIN_FOOTER );

    // set auto page breaks
    $pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );

    // set image scale factor
    $pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

    // ---------------------------------------------------------

    // set default font subsetting mode
    $pdf->setFontSubsetting( true );

    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont( 'helvetica', '', 10, '', true );

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

    // Block 1
    $styles = $this->get_pdf_styles();
    $pdf->SetXY( PDF_MARGIN_LEFT, 40 );

    $spare_parts = POR_Core::instance()->helpers->get_spare_parts_compatible_models( $pdf_post->post_title );

    ob_start(); ?>
    <h1>Spare Parts List</h1>
    <table cellspacing="0" cellpadding="5" border="1">
      <tr>
        <td><?= $pdf_post->post_title ?></td>
        <td><?= $pdf_post->post_content ?></td>
      </tr>
    </table>

    <p>&nbsp;</p>

    <table cellspacing="0" cellpadding="5" border="1">
      <thead>
      <tr>
        <td class="thead col-number">Part #</td>
        <td class="thead col-description">Description</td>
        <td class="thead col-qty">BOM Qty</td>
      </tr>
      </thead>
      <tbody>
      <?php foreach ( $spare_parts as $spare_part ) : ?>
        <tr>
          <td class="col-number"><?= $spare_part['partNumber'] ?></td>
          <td class="col-description"><?= $spare_part['partDescription'] ?></td>
          <td class="col-qty"><?= $spare_part['partQuantity'] ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?= $styles ?>
    <?php
    $pdf->writeHTML( ob_get_clean(), true, false, true );

    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output( sprintf( 'parts-list--%2$s--%1$s.pdf', wp_date( 'dmY' ), $pdf_post->post_name ), 'D' );

    //============================================================+
    // END OF FILE
    //============================================================+
  }

  function get_pdf_styles(): string {
    ob_start();
    ?>
    <style>
        .thead {
            font-weight: bold;
            background-color: #d3d3d3;
        }

        .col-qty {
            width: 10%;
        }

        .col-description {
            width: 65%;
        }

        .col-number {
            width: 25%;
        }
    </style>
    <?php
    return ob_get_clean();
  }
}

// Include the main TCPDF library (search for installation path).
require_once( locate_template( 'inc/TCPDF/tcpdf.php' ) );

// Extend the TCPDF class to create custom Header and Footer
class Mitsubishi_PDF extends TCPDF {
  //Page header
  public function Header() {
    // get the current page break margin
    $bMargin = $this->getBreakMargin();

    // get current auto-page-break mode
    $auto_page_break = $this->AutoPageBreak;

    // disable auto-page-break
    $this->SetAutoPageBreak( false, 0 );

    // set background image
    $img_file = K_PATH_IMAGES . 'logo-full.png';
    $this->Image( $img_file, 12, 10, 40, '' );

    // restore auto-page-break status
    $this->SetAutoPageBreak( $auto_page_break, $bMargin );

    // set the starting point for the page content
    $this->setPageMark();
  }
}
