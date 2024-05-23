<?php 

if (!isset($_SESSION['CUSID'])){
redirect(web_root."index.php");
}
 

     

$customerid =$_SESSION['CUSID'];
$customer = New Customer();
$singlecustomer = $customer->single_customer($customerid);

  ?>

<?php 
  $autonumber = New Autonumber();
  $res = $autonumber->set_autonumber('ordernumber'); 
?>




<form onsubmit="return orderfilter()" action="customer/controller.php?action=processorder" method="post">
    <div class="row">
        <div class="col-md-6 pull-left">
            <div class="col-md-2 col-lg-2 col-sm-2" style="float:left">
                Name:
            </div>
            <div class="col-md-8 col-lg-10 col-sm-3" style="float:left">
                <?php echo $singlecustomer->FNAME .' '.$singlecustomer->LNAME; ?>
            </div>
            <div class="col-md-2 col-lg-2 col-sm-2" style="float:left">
                Address:
            </div>
            <div class="col-md-8 col-lg-10 col-sm-3" style="float:left">
                <?php echo $singlecustomer->CUSHOMENUM . ' ' . $singlecustomer->STREETADD . ' ' .$singlecustomer->BRGYADD . ' ' . $singlecustomer->CITYADD . ' ' .$singlecustomer->PROVINCE . ' ' .$singlecustomer->COUNTRY; ?>
            </div>
        </div>

        <div class="col-md-6 pull-right">
            <div class="col-md-10 col-lg-12 col-sm-8">
                <input type="hidden" value="<?php echo $res->AUTO; ?>" id="ORDEREDNUM" name="ORDEREDNUM">
                Order Number :<?php echo $res->AUTO; ?>
            </div>
        </div>
    </div>
    <hr />
    <div class="table-responsive">

        <table class="gcCentrum-table table-hover" id="table" style="width: 100%;">
            <thead>
                <tr>
                    <!-- <th width="10">#</th> -->
                    <th style="width:12%; text-align: center; padding: 1rem 0;">Foods</th>
                    <th style="text-align: center; padding: 1rem 0;">Description</th>
                    <th style="width:15%; text-align: center; padding: 1rem 0;">Quantity</th>
                    <th style="width:15%; text-align: center; padding: 1rem 0;">Price</th>
                    <th style="width:30%; text-align: center; padding: 1rem 0;">Total</th>
                </tr>
            </thead>
            <tbody>

                <?php

              $tot = 0;
                if (!empty($_SESSION['gcCart'])){ 
                      $count_cart = @count($_SESSION['gcCart']);
                      for ($i=0; $i < $count_cart  ; $i++) { 

                      $query = "SELECT * FROM `tblpromopro` pr , `tblproduct` p , `tblcategory` c
                           WHERE pr.`PROID`=p.`PROID` AND  p.`CATEGID` = c.`CATEGID`  and p.PROID='".$_SESSION['gcCart'][$i]['productid']."'";
                        $mydb->setQuery($query);
                        $cur = $mydb->loadResultList();
                        foreach ($cur as $result){ 
              ?>

                <tr style="text-align: center;">
                    <!-- <td></td> -->
                    <td style="margin-bottom: 12px;"><img src="admin/products/<?php echo $result->IMAGES ?>"
                            width="100px" height="100px"></td>
                    <td style="margin-bottom: 12px;"><?php echo $result->PRODESC ; ?></td>
                    <td style="margin-bottom: 12px;"><?php echo $_SESSION['gcCart'][$i]['qty']; ?></td>
                    <td style="margin-bottom: 12px;">&#8369 <?php echo  $result->PRODISPRICE ?></td>
                    <td style="margin-bottom: 12px;">&#8369
                        <output><?php echo $_SESSION['gcCart'][$i]['price']?></output>
                    </td>
                </tr>
                <?php
              $tot +=$_SESSION['gcCart'][$i]['price'];
                        }

                      }
                }
              ?>


            </tbody>

        </table>
    </div>
    <hr />
    <div class="row" style="padding: 2rem;">
        <div class="col-md-8">
            <div class="form-group">
                <label> Payment Method : </label>
                <!-- <div class="radio">
                      <label>
                    
                          <input type="radio" class="paymethod " data-toggle="collapse"  data-parent="#accordion"  data-target="#collapseTwo" name="paymethod" id="pickupfee" value="Cash on Pickup" checked>Cash on Pickup 
                       
                      </label>
                  </div> -->
                <div class="radio">
                    <label>
                        <input type="radio" class="paymethod" name="paymethod" id="deliveryfee" value="Cash on Delivery"
                            checked="true" data-toggle="collapse" data-parent="#accordion"
                            data-target="#collapseOne">Cash on Delivery
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" class="paymethod" name="paymethod" id="deliveryfee" value="GCash"
                            checked="true" data-toggle="collapse" data-parent="#accordion"
                            data-target="#collapseOne">GCash
                    </label>
                </div>




                <!-- <div class="panel-group" id="accordion"> -->
                <div class="panel">
                    <!-- <div class="panel-heading">
                                <h4 class="panel-title"> 
                                   <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Cash on Delivery</a>
                                </h4>
                            </div> -->
                    <!--    <div id="collapseTwo" class="panel-collapse collapse">
                              
                            </div> -->
                    <!-- <div id="collapseOne" class="panel-collapse collapse active"> -->
                    <!-- <div class="panel-body">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label class="col-md-8 control-label" for="PLACE">Type of Customer</label>
                                <select class="form-control paymethod" name="PLACE" id="PLACE"
                                    onchange="validatedate()">
                                    <option value="0" selected disabled>Select</option>
                                    <?php 
                                            $query = "SELECT * FROM `tblsetting` ";
                                            $mydb->setQuery($query);
                                            $cur = $mydb->loadResultList();

                                            foreach ($cur as $result) {  
                                              echo '<option value='.$result->DELPRICE.'>'.$result->BRGY.' '.$result->PLACE.' </option>';
                                            }
                                            ?>
                                </select>
                            </div>

                        </div>

                    </div> -->
                </div>
                <!--   </div>
                   </div>             
                    -->

                <input type="hidden" placeholder="HH-MM-AM/PM" id="CLAIMEDDATE" name="CLAIMEDDATE"
                    value="<?php echo date('y-m-d h:i:s') ?>" class="form-control" />

            </div>


            <div class="col-md-5" style="margin-bottom: 4rem;">
                <p align="right">
                <div> Total Price : &#8369<span id="sum">0.00</span></div>
                <div> Discount: &#8369<span id="fee">0.00</span></div>
                <div> Overall Price : &#8369<span id="overall"
                        style="font-weight: 700; font-size: 16px;"><?php echo $tot ;?></span></div>
                <input type="hidden" name="alltot" id="alltot" value="<?php echo $tot ;?>" />
                </p>
            </div>
        </div>
        <br />
        <div class="row">
            <div class="col-md-6">
                <a href="index.php?q=cart" class="btn btn-default pull-left"><i class="fa fa-chevron-left"
                        aria-hidden="true"></i>
                    &nbsp;<strong>View
                        Cart</strong></a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-pup  pull-right " name="btn" id="btn"
                    onclick="return validatedate();" /> Submit Order <i class="fa fa-shopping-cart"></i></button>
            </div>
        </div>


</form>