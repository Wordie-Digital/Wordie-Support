<?

global $post;

$alm_item = $alm_item ?? 0;

switch ( $post->post_type ) {
  case 'cpt-resource':
    $layout = isset( $args['alm_vars'], $args['alm_vars']['layout'] ) ? $args['alm_vars']['layout'] : 'cards';

    switch ( $layout ) {
      case 'list':
        ?>
        <div class="col">
          <?
          get_template_part( 'template-parts/download-line/download-line' ); ?>
        </div>
        <?php
        break;

      default:
        ?>
        <div class="col mb-5">
          <?
          get_template_part( 'template-parts/post-resource/post-resource' ) ?>
        </div>
      <?php
    }
    break;

  case 'product':
    ?>
    <div class="col mb-5">
      <?
      get_template_part( 'template-parts/post-product/post-product' ) ?>
    </div>
    <?php
    break;

  case 'cpt-spare-part':
    get_template_part( 'template-parts/post-spare-part/post-spare-part', null, [
      'index'      => $alm_item,
      'etaModalId' => isset( $args['alm_vars'], $args['alm_vars']['etaModalId'] ) ? $args['alm_vars']['etaModalId'] : '',
    ] );
    break;

  case 'cpt-material':
    get_template_part( 'template-parts/post-spare-part/post-material', null, [ 'index' => $alm_item ] );
    break;

  default:
    ?>
    <div class="col mb-5">
      <?
      get_template_part( 'template-parts/post-generic/post-generic' ) ?>
    </div>
  <?php
}
