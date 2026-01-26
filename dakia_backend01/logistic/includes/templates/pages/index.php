<?php include 'header.html'; ?>
<body class="slide" >
<div class="wraper">
<div class="inner-wraper">

<?php MainMenu::getItem()->render(); ?>    

<!--header-->
<div class="inner-wraper1">
<section>
	
	<!--left bar-->
    
    <?php $this->renderLeftColumn(); ?>
    
    <!--centre bar-->
  
		<?php $this->renderTitle(); ?>
		
		<?php $this->renderBody(); ?>

    <!--center-bar-->
      
    <!--right-bar-->
  
    <?php $this->renderRightColumn(); ?>

    <!--right bar end-->
    
    <br class="clearfloat" />    

    <div class="partner">
    	<h1>PARTNERS</h1>
        <img src="/App_Themes/images/partner-01.jpg" />
        <img src="/App_Themes/images/partner-05.jpg" />
        <img src="/App_Themes/images/partner-06.jpg" />
        <img src="/App_Themes/images/partner-08.jpg" />

    </div><!--partner-->
</section>

<!--container-->


</div>
</div>
</div>
<div class="clear"></div>
<?php Footer::getItem()->render(); ?>

</body>
</html>
