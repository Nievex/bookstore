 <style type="text/css">
#home {
    width: 100%;
}

#home>tr {
    width: 50px;
}

.logo {
    width: 150px;
}


.logo>img {
    height: 100%;
    width: 100%;

}

.morecontent span {
    display: block;
}

.morelink {
    display: block;
}

.book-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4rem;
}

@media (max-width: 618px) {
    .book-grid {
        grid-template-columns: repeat(1, 1fr);
    }

    .logo {
        width: 120px;
    }
}
 </style>

 <!-- Projects Row -->
 <div class="row">
     <div class="col-md-12 book-grid">

         <?php 
          $query = "SELECT * FROM `tblproduct` p  ,`tblcategory` c  WHERE   p.`CATEGID`=c.`CATEGID` AND PROQTY>0 ";
          $mydb->setQuery($query);
          $cur = $mydb->loadResultList(); 
          foreach ($cur as $result) { ?>

         <div class="col-lg-6" style="display: flex; font-weight: 700; gap: 1rem; width: 100%;">
             <a href="index.php?q=single-item&id=<?php echo $result->PROID; ?>">
                 <div class="logo">
                     <img class="img-hover" src="<?php echo web_root.'admin/products/'. $result->IMAGES; ?>"
                         alt="<?php echo $result->CATEGORIES; ?>">
                     <input type="hidden" name="" value="<?php echo $result->PROID; ?>" id="PROID">
                 </div>
             </a>
             <span class="more">
                 <?php echo $result->PRODESC  ; ?>
             </span>
         </div>
         <?php  }?>
     </div>
 </div>