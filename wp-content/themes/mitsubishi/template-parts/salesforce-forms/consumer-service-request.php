<?php

defined( 'ABSPATH' ) or exit;

?>
<div class="mit-salesforce-forms">
  <form method="post" action="#" id="form-consumer-service-request">
    <input type=hidden value=00D90000000xjc4 name=orgid>

    <?php if ( ! empty( $service_request_received_page = get_field( 'service_request_received_page', 'options' ) ) ) : ?>
      <input type=hidden value="<?= get_permalink( $service_request_received_page ) ?>" name=retURL>
    <?php endif; ?>

    <div class="row">
      <div class=col-md-12>
        <h4>Product Type</h4>
      </div>
      <div class=col-md-6>
        <div class=form-group>
          <label class=gray-text for=00NN0000001HiAu>Product Type Level 1: <span class=red-text>*</span></label>
          <select id=00NN0000001HiAu title="Product Type 1" class="form-select gray-text" required name=00N9000000Dfrqi>
            <option selected>-- None --</option>
            <option value=Refrigerator>Refrigerator</option>
            <option value="Air Conditioner">Air Conditioner</option>
            <option value="Electric Fan">Electric Fan</option>
            <option value=WiFi>WiFi</option>
            <option value="Visual Display">Visual Display</option>
            <option value="Air Curtain">Air Curtain</option>
          </select></div>
      </div>
      <div class=col-md-6>
        <div class=form-group>
          <label class=gray-text for=00NN0000001HiAv>Product Type Level 2: <span class=red-text>*</span></label>
          <select id=00NN0000001HiAv title="Product Type 2" class="form-select gray-text" required name=00N9000000Dfrqj>
            <option selected>-- None --</option>
            <option value=Refrigerator>Refrigerator</option>
            <option value="Air Conditioner">Air Conditioner</option>
            <option value="Electric Fan">Electric Fan</option>
            <option value=WiFi>WiFi</option>
            <option value="Visual Display">Visual Display</option>
            <option value="Air Curtain">Air Curtain</option>
          </select>
        </div>
      </div>

      <div class=col-md-12>
        <h4>Contact Personal Details</h4>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBN>Title:</label>
          <select id=00NN0000001HiBN class=form-select name=00N9000000DfrrB>
            <option selected>--None--</option>
            <option value=Mr.>Mr.</option>
            <option value=Mrs.>Mrs.</option>
            <option value=Ms.>Ms.</option>
            <option value=Dr.>Dr.</option>
            <option value=Prof.>Prof.</option>
          </select></div>
      </div>

      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBB>First Name: <span class=red-text>*</span></label> <input required id=00NN0000001HiBB class=form-control maxLength=100 name=00N9000000Dfrqz></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBE>Last Name: <span class=red-text>*</span></label> <input required id=00NN0000001HiBE class=form-control maxLength=100 name=00N9000000Dfrr2></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label id=telLabel class=gray-text for=phone>Telephone: <span class=red-text>*</span></label> <input required id=phone class="form-control phone-group" maxLength=40 name=phone data-inputmask="'mask': '(99) 9999-9999'"></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label id=mobLabel class=gray-text for=mobile_phone>Mobile: <span class=red-text>*</span></label> <input required id=mobile_phone class="form-control phone-group" maxLength=40 name=00N9000000Dfrr3 data-inputmask="'mask': '+61499-999-999'"></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=email>Email: <span class=red-text>*</span></label> <input required id=email class=form-control name=email></div>
      </div>
      <div class=col-md-12>
        <h4>Installation Address</h4></div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBC>House Number:</label> <input id=00NN0000001HiBC class=form-control maxLength=10 name=00N9000000Dfrr0></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBL>Street:</label> <input id=00NN0000001HiBL class=form-control maxLength=100 name=00N9000000Dfrr9></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBM>Suburb:</label> <input id=00NN0000001HiBM class=form-control maxLength=100 name=00N9000000DfrrA></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBK>State:</label>
          <select id=00NN0000001HiBK class="form-select gray-text" name=00N9000000Dfrr8>
            <option selected>--None--</option>
            <option value=Queensland>Queensland</option>
            <option value="South Australia">South Australia</option>
            <option value=Tasmania>Tasmania</option>
            <option value=Victoria>Victoria</option>
            <option value="Western Australia">Western Australia</option>
            <option value="New South Wales">New South Wales</option>
            <option value="Northern Territory">Northern Territory</option>
          </select></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBH>Postcode:</label> <input id=00NN0000001HiBH class=form-control maxLength=4 name=00N9000000Dfrr5></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBI>Product Model Number:</label> <input id=00NN0000001HiBI class=form-control maxLength=20 name=00N9000000Dfrr6></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBJ>Serial Number:</label> <input id=00NN0000001HiBJ class=form-control maxLength=20 name=00N9000000Dfrr7></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NO0000001eZYq>Date Of Purchase:</label> <input id=00NO0000001eZYq class=form-control maxLength=100 type=date name=00N9000000DfrqR></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=00NN0000001HiBG>Place of Purchase:</label> <input id=00NN0000001HiBG class=form-control maxLength=255 name=00N9000000Dfrr4></div>
      </div>
      <div class=col-md-6>
        <div class=form-group><label class=gray-text for=subject>Subject: <span class=red-text>*</span></label> <input required id=subject class=form-control maxLength=80 name=subject></div>
      </div>
      <div class=col-md-12>
        <div class=form-group><label class=gray-text for=description>Description: <span class=red-text>*</span></label> <textarea required class=form-control name=description id=description></textarea></div>
      </div>
      <div class=col-md-6 style="display: none"><!-- style="display: none;" -->
        <div class=form-group><label class=gray-text for=00NN0000001HiBA>End User:</label> W2C End User:<input id=00NN0000001HiBA CHECKED type=checkbox value=1 name=00N9000000Dfrqy></div>
      </div>
      <div class=col-md-6>
        <button class="btn btn--default" type=submit name=submit>Submit</button>
      </div>
    </div>
  </form>
</div>
