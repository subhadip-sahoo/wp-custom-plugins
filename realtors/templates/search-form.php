<div class="realtors-wrapper">
    <div class="alert alert-danger" style="display: none;">
        Please enter at least any one of the search fields!
    </div>
    <form class="realtorsform" name="search-realtors" id="search-realtors" action="<?php echo $action; ?>" method="GET" role="form">
        <div class="form-group col-md-3">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control realtors" id="first_name" name="first_name" value="<?php echo $req['first_name']; ?>" placeholder="Search with first name">
        </div>
        <div class="form-group col-md-3">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control realtors" id="last_name" name="last_name" value="<?php echo $req['last_name']; ?>" placeholder="Search with last name">
        </div>
        <div class="form-group col-md-3">
            <label for="email">Email Address</label>
            <input type="email" class="form-control realtors" id="email" name="email" value="<?php echo $req['email']; ?>" placeholder="Search with email">
        </div>
        <div class="form-group col-md-2">
            <label for="zipcode">Zipcode</label>
            <input type="text" class="form-control realtors" id="zipcode" name="zipcode" value="<?php echo $req['zipcode']; ?>" placeholder="Search with zipcode">
        </div>
        <div class="form-group col-md-1">
            <label></label>
            <input type="submit" class="btn btn-default" value="Search" id="btn-search"/>
        </div>
    </form> 
</div>