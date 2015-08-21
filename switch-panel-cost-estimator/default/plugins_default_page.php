<?php
    function plugins_default_page(){
        echo '<h2>Designed and developed by <a href"http://qss.in">INDIASOFTWARETEAM</a></h2>';
        echo '<p class="description">Set your settings <a href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=plugin-custom-settings').'">here</a>.</p>';
        echo '<p class="description">Please put [switch-panel-cost-estimator] into post or pages to generate switch panel cost estimator form calculator.</p>';
    }
?>
