<div class="col-md-12" id="realtors-list">
    <h2>Realtors lists</h2>
    <div class="list-group">
        <?php foreach($results as $result) : ?>
        <a href="#" class="list-group-item realtors-list-item">
            <h4 class="list-group-item-heading">Name : <?php echo $result->full_name; ?></h4>
            <p class="list-group-item-text">Member Number : <?php echo $result->member_number; ?></p>
            <p class="list-group-item-text">MLS ID : <?php echo $result->mls_id; ?></p>
            <p class="list-group-item-text">Bill Type Code : <?php echo $result->bill_type_code; ?></p>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<div class="col-md-8"></div>
