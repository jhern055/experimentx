<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" id="sidebar-wrapper">

    <div class="left-sidebar">

        <div id="cssmenu">
        <h2>Categorias</h2>
        <?php echo $menuCategory; ?>
        </div> 
                               
        <div class="price-range"><!--price-range-->
            <h2>Price Range</h2>
            <div class="well text-center">
                 <input type="text" class="span2" value="" data-slider-min="0" data-slider-max="600" data-slider-step="5" data-slider-value="[250,450]" id="sl2" ><br />
                 <b class="pull-left">$ 0</b> <b class="pull-right">$ 600</b>
            </div>
        </div><!--/price-range-->
        
        <div class="shipping text-center"><!--shipping-->
            <img src="<?php echo base_url(); ?>images/template/ecommerce/home/shipping.jpg" alt="" />
        </div><!--/shipping-->
            
    </div>
</div>