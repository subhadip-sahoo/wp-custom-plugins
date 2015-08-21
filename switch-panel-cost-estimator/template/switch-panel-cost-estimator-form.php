<?php 
    require_once dirname(__FILE__).'/includes/template_functions.php';
    require_once dirname(__FILE__).'/includes/get_estimate.php';
?>
<div class="sf_content col2left container">
    <div class="row-fluid">
      <div class="span6">
        <h1>Switch Panel Cost Estimator</h1>
        <style>.btn {font-size: 15px; height: 3em;}</style>
        <?php 
            if(!empty($err_msg)){
                echo '<p style="color:red;">'.$err_msg.'</p>';
            }
            else if(!empty ($suc_msg)){
                echo $suc_msg;
            }
        ?>
            <form name="calculator" id="calculator" action="" method="post">
                <fieldset>
                  <legend>Contact Information</legend>
                  <label>Full name</label>
                  <input type="text" name="fullName" id="fullName" value="<?php echo(isset($fullName))?$fullName:'';?>" />
                  <label>Company</label>
                  <input type="text" name="company" id="company" value="<?php echo(isset($company))?$company:'';?>" placeholder="Optional"/>
                  <label>Email</label>
                  <input type="email" name="email" id="email" value="<?php echo(isset($email))?$email:'';?>" />
                  <label>Phone</label>
                  <input type="text" name="phone" id="phone" value="<?php echo(isset($phone))?$phone:'';?>" placeholder="Optional"/>
                </fieldset>
                <fieldset>
                  <legend>Request Information</legend>
                  <label>Request Type</label>
                  <select name="requestType" id="requestType">
                    <option value="0" <?php echo($requestType == 0)?'selected="selected"':'';?>>Select request type</option>
                    <option value="Personal" <?php echo($requestType == 'Personal')?'selected="selected"':'';?>>Personal</option>
                    <option value="Commercial (boat builder)" <?php echo($requestType == 'Commercial (boat builder)')?'selected="selected"':'';?>>Commercial (boat builder)</option>
                    <option value="Commercial (boat yard)" <?php echo($requestType == 'Commercial (boat yard)')?'selected="selected"':'';?>>Commercial (boat yard)</option>
                    <option value="Other" <?php echo($requestType == 'Other')?'selected="selected"':'';?>>Other</option>
                  </select>
                  <label>How many of these panels would you be looking to purchase?</label>
                  <select name="no_of_panels" id="no_of_panels">
                    <option value="1" <?php echo($no_of_panels == '1')?'selected="selected"':'';?>>1</option>
                    <option value="2 to 5" <?php echo($no_of_panels == '2 to 5')?'selected="selected"':'';?>>2 to 5</option>
                    <option value="5 to 10" <?php echo($no_of_panels == '5 to 10')?'selected="selected"':'';?>>5 to 10</option>
                    <option value="10 to 50" <?php echo($no_of_panels == '10 to 50')?'selected="selected"':'';?>>10 to 50</option>
                    <option value="50+" <?php echo($no_of_panels == '50+')?'selected="selected"':'';?>>50+</option>
                  </select>
                  <label>How did you hear about us?</label>
                  <label class="checkbox">
                      <input type="checkbox" name="how_did_you_hear_about_us[]" id="Google" value="Google" <?php echo(isset($_POST['how_did_you_hear_about_us']) && in_array('Google', $_POST['how_did_you_hear_about_us']))?'checked':'';?> />Google
                  </label>
                  <label class="checkbox">
                    <input type="checkbox" name="how_did_you_hear_about_us[]" id="Facebook" value="Facebook" <?php echo(isset($_POST['how_did_you_hear_about_us']) && in_array('Facebook', $_POST['how_did_you_hear_about_us']))?'checked':'';?>/>Facebook
                  </label>
                  <label class="checkbox">
                    <input type="checkbox" name="how_did_you_hear_about_us[]" id="Other-search-engine" value="Other search engine" <?php echo(isset($_POST['how_did_you_hear_about_us']) && in_array('Other search engine', $_POST['how_did_you_hear_about_us']))?'checked':'';?>/>Other search engine
                  </label>
                  <label class="checkbox">
                    <input type="checkbox" name="how_did_you_hear_about_us[]" id="Forum" value="Forum" <?php echo(isset($_POST['how_did_you_hear_about_us']) && in_array('Forum', $_POST['how_did_you_hear_about_us']))?'checked':'';?>/>Forum
                  </label>
                  <label class="checkbox">
                    <input type="checkbox" name="how_did_you_hear_about_us[]" id="eBay" value="eBay" <?php echo(isset($_POST['how_did_you_hear_about_us']) && in_array('eBay', $_POST['how_did_you_hear_about_us']))?'checked':'';?>/>eBay
                  </label>
                  <label class="checkbox">
                    <input type="checkbox" name="how_did_you_hear_about_us[]" id="Word-of-mouth" value="Word of mouth" <?php echo(isset($_POST['how_did_you_hear_about_us']) && in_array('Word of mouth', $_POST['how_did_you_hear_about_us']))?'checked':'';?>/>Word of mouth
                  </label>
                  <label class="checkbox">
                    <input type="checkbox" name="how_did_you_hear_about_us[]" id="Other" value="Other" <?php echo(isset($_POST['how_did_you_hear_about_us']) && in_array('Other', $_POST['how_did_you_hear_about_us']))?'checked':'';?>/>Other
                  </label>
                  <div id="heard_from_other" style="display:none;">
                    <span>
                      <input type="text" name="other_option" id="other_option" placeholder="How you heard about us" autofocus="autofocus" value="<?php echo(isset($other_option))?$other_option:'';?>"/>
                    </span>
                  </div>
                  <label>Would you like a New Wire Marine representative to contact you?</label>
                  <select name="wouldLikeContact" id="wouldLikeContact">
                    <option value="Yes" <?php echo($wouldLikeContact == 'Yes')?'selected="selected"':'';?>>Yes</option>
                    <option value="Not at this time" <?php echo($wouldLikeContact == 'Not at this time')?'selected="selected"':'';?>>Not at this time</option>
                  </select>
                  <label>Please tell us about your project</label>
                  <textarea rows="6" name="projectDetails" id="projectDetails" placeholder="Optional"><?php echo(isset($projectDetails))?$projectDetails:'';?></textarea>
                </fieldset>
                
                <!-- Need to dynamic -->
                <fieldset id="switch-panel">
                    <legend><?php echo get_option('switch_panel_legend');?></legend>
                        <?php echo switch_panel_configuration();?>
                  <hr/>
                  <p>Please fill out all required fields.</p>
                  <div>
                    <input type="submit" name="get_estimate" value="Get Estimate" class="btn btn-primary btn-large btn-block nwm-button" />
                  </div>
                </fieldset>
                <!-- End -->
            <?php 
                if(isset($show_estimate) && $show_estimate == TRUE){
            ?>
                <div>
                  <h2>Your Estimate</h2>
                  <div class="well">
                      <p class="lead">
                          Your panel estimate is <strong>$<?php echo number_format($low_end_price, 2, '.', '');?></strong> to <strong> $<?php echo number_format($high_end_price, 2, '.', '');?></strong>
                      </p>
                      <span>
                    <?php
                        if(isset($wouldLikeContact) && $wouldLikeContact == 'Yes'){
                    ?>
                        <p>A New Wire Marine Representative will be contacting you shortly.</p>
                      </span>
                    <?php } ?>
                  </div>
                </div>
            <?php 
                }
            ?>
            </form>
        </div>
      <div class="span6 well">
        <div>
            <h1 class="ng-scope">Custom Boat Switch Panels</h1>
            <span class="ng-scope"></span>
            <p class="ng-scope">New Wire Marine builds fully custom DC switch panels for both private and commercial customers. Our prices, lead time and quality are unmatched. We are confidant you will be extremely happy with the custom panel we build for your boat.</p>
            <span class="ng-scope"></span>
            <p class="ng-scope">This calculator will give you a ballpark price for a custom switch panel, designed and assembled for your boat. Contact us for a full quote and lead time estimate.</p>
            <span class="ng-scope"></span>
            <img class="img-polaroid ng-scope" src="<?php echo plugins_url();?>/switch-panel-cost-estimator/template/img/custom_boat_switch_panels_1.jpg">
            <span class="ng-scope"></span>
            <p class="ng-scope">Our switch panels can be cut from various material colors and in nearly any size and shape you can imagine.</p>
            <span class="ng-scope"></span>
            <img class="img-polaroid ng-scope" src="<?php echo plugins_url();?>/switch-panel-cost-estimator/template/img/custom_boat_switch_panels_2.jpg">
            <span class="ng-scope"></span>
            <p class="ng-scope">There are many switch, breaker and lighting options.</p>
            <span class="ng-scope"></span>
            <img class="img-polaroid ng-scope" src="<?php echo plugins_url();?>/switch-panel-cost-estimator/template/img/custom_boat_switch_panels_3.jpg">
            <span class="ng-scope"></span>
            <p class="ng-scope">We specialize in boat switch panels, but there are tons of other applications, we've built panels for anything from RV's, to jeeps, to golf carts.</p>
            <span class="ng-scope"></span>
            <img class="img-polaroid ng-scope" src="<?php echo plugins_url();?>/switch-panel-cost-estimator/template/img/custom_boat_switch_panels_4.jpg">
            <span class="ng-scope"></span>
            <p class="ng-scope">Panels are fully wired and ready to be installed, or we can do blank plates and partially assembled panels.</p>
            <span class="ng-scope"></span>
            <img class="img-polaroid ng-scope" src="<?php echo plugins_url();?>/switch-panel-cost-estimator/template/img/custom_boat_switch_panels_5.jpg">
            <span class="ng-scope"></span>
            <p class="ng-scope">Stunning logo engraving and LED and fiber optic backlighting are available.  Imagine your boat name or company's logo engraved into the switch panel? Flip a switch and the logo or text can illuminate</p>
            <span class="ng-scope"></span>
            <img class="img-polaroid ng-scope" src="<?php echo plugins_url();?>/switch-panel-cost-estimator/template/img/custom_boat_switch_panels_6.jpg">
            <span class="ng-scope"></span>
            <p class="ng-scope">If you don't see exactly what you're looking for, contact us to see if we can build it.</p>
            <span class="ng-scope"></span>
            <p class="ng-scope">* The estimate is for a one-off single panel with all associated setup work. Boat builders contact us for volume pricing info. *</p>
            <span class="ng-scope"></span>
        </div>
      </div>
    </div>      
</div>