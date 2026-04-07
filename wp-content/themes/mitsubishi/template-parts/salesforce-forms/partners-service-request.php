<?php

defined( 'ABSPATH' ) or exit;

?>
<div class="mit-salesforce-forms">
  <form method="post" action="#" id="form-partners-service-request">
    <input type=hidden value=00d90000000xjc4 name=orgid>

    <?php if ( ! empty( $service_request_received_page = get_field( 'service_request_received_page', 'options' ) ) ) : ?>
      <input type=hidden value="<?= get_permalink( $service_request_received_page ) ?>" name=retURL>
    <?php endif; ?>

    <div class="row">
      <div class=col-md-12>
        <h4>Product Type</h4>
      </div>
      <div class=col-md-6>
        <div class=form-group>
          <label class=gray-text for=00nn0000001hiau>Product type level 1: <span class=red-text>*</span></label>
          <select id=00nn0000001hiau title="Product type 1" class=form-select required name=00n9000000dfrqi>
            <option selected>-- none --</option>
            <option value=refrigerator>Refrigerator</option>
            <option value="air conditioner">Air conditioner</option>
            <option value="electric fan">Electric fan</option>
            <option value=wifi>Wifi</option>
            <option value="visual display">Visual display</option>
            <option value="air curtain">Air curtain</option>
          </select>
        </div>
      </div>

      <div class=col-md-6>
        <div class=form-group>
          <label class=gray-text for=00nn0000001hiav>Product type level 2: <span class=red-text>*</span></label>
          <select id=00nn0000001hiav title="product type 2" class="form-select gray-text" required name=00n9000000dfrqj>
            <option selected>-- none --</option>
            <option value=refrigerator>Refrigerator</option>
            <option value="air conditioner">Air conditioner</option>
            <option value="electric fan">Electric fan</option>
            <option value=wifi>Wifi</option>
            <option value="visual display">Visual display</option>
            <option value="air curtain">Air curtain</option>
          </select>
        </div>
      </div>

      <div class=col-md-12>
        <h4>Contact personal details</h4>
      </div>

      <div class=col-md-6>
        <div class=form-group>
          <label class=gray-text for=00nn0000001hibn>title:</label>
          <select id=00nn0000001hibn class="form-select gray-text" name=00n9000000dfrrb>
            <option selected>--None--</option>
            <option value=mr.>Mr.</option>
            <option value=mrs.>Mrs.</option>
            <option value=ms.>Ms.</option>
            <option value=dr.>Dr.</option>
            <option value=prof.>Prof.</option>
          </select>
        </div>
      </div>
      <div class=col-md-6>
        <div class=form-group>
          <label class=gray-text for=nn0000001hibb>First name: <span class=red-text>*</span></label>
          <input id=nn0000001hibb class=form-control maxlength=100 name=00n9000000dfrqz required minlength="3">
        </div>
      </div>

      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00nn0000001hibe>Last name: <span class=red-text>*</span></label> <input required id=00nn0000001hibe class=form-control maxlength=100 name=00n9000000dfrr2></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label id=tellabel class=gray-text for=phone>Telephone: <span class=red-text>*</span></label> <input required id=phone class="form-control phone-group" maxlength=40 name=phone data-inputmask="'mask': '(99) 9999-9999'"></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label id=moblabel class=gray-text for=mobile_phone>Mobile: <span class=red-text>*</span></label> <input required id=mobile_phone class="form-control phone-group" maxlength=40 name=00n9000000dfrr3 data-inputmask="'mask': '+61499-999-999'"></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=email>Email: <span class=red-text>*</span></label> <input required id=email class=form-control name=email></div>
      </div>

      <div class=col-md-12>
        <h4>installation address</h4>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00nn0000001hibc>House number:</label> <input id=00nn0000001hibc class=form-control maxlength=10 name=00n9000000dfrr0> <!--<span class="help-block">data-inputmask="'mask': '(999) 999-9999"'</span>--></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00nn0000001hibl>Street:</label> <input id=00nn0000001hibl class=form-control maxlength=100 name=00n9000000dfrr9></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00nn0000001hibm>Suburb:</label> <input id=00nn0000001hibm class=form-control maxlength=100 name=00n9000000dfrra></div>
      </div>
      <div class=col-md-6>
        <div class=form-group>
          <label class=gray-text for=00nn0000001hibk>State:</label>
          <select id=00nn0000001hibk class=form-control name=00n9000000dfrr8>
            <option selected>--None--</option>
            <option value=queensland>Queensland</option>
            <option value="south australia">South Australia</option>
            <option value=tasmania>Tasmania</option>
            <option value=victoria>Victoria</option>
            <option value="western australia">Western Australia</option>
            <option value="new south wales">New South Wales</option>
            <option value="northern territory">Northern Territory</option>
          </select>
        </div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00nn0000001hibh>Postcode:</label> <input id=00nn0000001hibh class=form-control maxlength=4 name=00n9000000dfrr5></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00nn0000001hibi>Product model number:</label> <input id=00nn0000001hibi class=form-control maxlength=20 name=00n9000000dfrr6></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00nn0000001hibj>Serial number:</label> <input id=00nn0000001hibj class=form-control maxlength=20 name=00n9000000dfrr7></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00no0000001ezyq>Date of purchase:</label> <input id=00no0000001ezyq class=form-control maxlength=100 type=date name=00n9000000dfrqr></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00nn0000001hibg>Place of purchase:</label> <input id=00nn0000001hibg class=form-control maxlength=255 name=00n9000000dfrr4></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=subject>Subject: <span class=red-text>*</span></label> <input required id=subject class=form-control maxlength=80 name=subject></div>
      </div>
      <div class=col-md-12>
        <div class=form-group><label class=gray-text for=description>Description: <span class=red-text>*</span></label> <textarea required class=form-control name=description></textarea></div>
      </div>
      <div class=col-md-12>
        <div class=checkbox><label for=00nn0000001hibd><input id=00nn0000001hibd type=checkbox value=1 name=00n9000000dfrr1>I do not have an account: </label></div>
      </div>
      <div id=accountnum class=col-md-6>
        <div class=form-group><label class=gray-text for=00nn0000001hib8>Account number:</label> <input id=00nn0000001hib8 class=form-control maxlength=255 name=00n9000000dfrqw></div>
      </div>
      <div class=col-md-6>
        <div class=form-group>
          <label class=gray-text for=00nn0000001hib7>Company name: <span class=red-text>*</span></label>
          <input required id=00nn0000001hib7 class=form-control maxlength=255 name=00n9000000dfrqv>
        </div>
      </div>
      <div class=col-md-6 style="display: none">
        <div class=form-group><label for=00nn0000001hib9>W2c commercial:</label> <input id=00nn0000001hib9 checked type=checkbox value=1 name=00n9000000dfrqx></div>
      </div>

      <div class=col-md-12>
        <button class="btn btn--default" type=submit name=submit>Submit</button>
      </div>
    </div>
  </form>
</div>
