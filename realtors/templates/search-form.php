<div class="realtors-wrapper">
    <div class="alert alert-danger" style="display: none;">
        Please enter at least any one of the search fields!
    </div>
    <form class="realtorsform" name="search-realtors" id="search-realtors" action="<?php echo $action; ?>" method="GET" role="form">
        <div class="row">
            <div class="form-group col-md-3">
                <label for="member_number">Member Number</label>
                <input type="text" class="form-control realtors" id="member_number" name="member_number" value="<?php echo $req['member_number']; ?>" placeholder="Search with member number">
            </div>
            <div class="form-group col-md-3">
                <label for="full_name">Full Name</label>
                <input type="text" class="form-control realtors" id="full_name" name="full_name" value="<?php echo $req['full_name']; ?>" placeholder="Search with full name">
            </div>
            <div class="form-group col-md-3">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control realtors" id="first_name" name="first_name" value="<?php echo $req['first_name']; ?>" placeholder="Search with first name">
            </div>
            <div class="form-group col-md-3">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control realtors" id="last_name" name="last_name" value="<?php echo $req['last_name']; ?>" placeholder="Search with last name">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-3">
                <label for="bill_type_code">Bill Type Code</label>
                <input type="text" class="form-control realtors" id="bill_type_code" name="bill_type_code" value="<?php echo $req['bill_type_code']; ?>" placeholder="Search with bill type code">
            </div>
            <div class="form-group col-md-3">
                <label for="mls_id">MLS Id</label>
                <input type="text" class="form-control realtors" id="mls_id" name="mls_id" value="<?php echo $req['mls_id']; ?>" placeholder="Search with mls id">
            </div>
            <div class="form-group col-md-3">
                <label for="lang">Language</label>
                <!--<input type="text" class="form-control realtors" id="mls_id" name="mls_id" value="<?php echo $req['mls_id']; ?>" placeholder="Search with mls id">-->
                <select name="lang" id="lang" class="form-control realtors">
                    <option value="">-- Search with language --</option>
                    <?php foreach ($langs as $code => $description): ?>
                    <option value="<?php echo $code; ?>" <?php echo ($req['lang'] == $code) ? 'selected="selected"' : ''; ?>><?php echo $description; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label></label>
                <input type="submit" class="btn btn-default" value="Search" id="btn-search"/>
            </div>
        </div>    
    </form> 
</div>