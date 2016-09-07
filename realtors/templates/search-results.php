<div class="col-md-12" id="realtors-list">
    <h2>Realtors lists</h2>
    <div class="list-group">
        <?php foreach($results as $result) : ?>
        <a href="#" class="list-group-item realtors-list-item">
            <h4 class="list-group-item-heading">Name : <?php echo $result->first_name . ' ' . $result->last_name; ?></h4>
            <p class="list-group-item-text">Email : <?php echo $result->email; ?></p>
            <p class="list-group-item-text">Zipcode : <?php echo $result->zipcode; ?></p>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<div class="col-md-8"></div>
