<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("Users");
$this->load->model("Userlogin");
$user_login = new Users();
$auth = httpFilterRequest("auth");
$user_login->selectByParams(array("A.USER_AUTH" => $auth));
// echo $user_login->query;
$user_login->firstRow();
$user_login_id = $user_login->getField('USER_LOGIN_ID');
$user_aktif = $user_login->getField('USER_AKTIF');
$user_auth = $user_login->getField('USER_AUTH');
?>
<section id="backColor">
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <div class="card" style="zoom: 1;">
        <div class="card-content collapse show">
            <div class="app-content content">
              <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                  <section class="flexbox-container">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-md-12 col-12 p-0">
                      <?php
                      if ($auth = $user_auth) {
                        if ($user_aktif != '1') {
                          $user_login_update = new Userlogin();
                          $user_login_update->setField("USER_LOGIN_ID", $user_login_id);
                          $user_login_update->setField("USER_AUTH", 'Akun sudah aktif: '.date('d-m-Y H:i:s'));
                          $user_login_update->aktivasiAkun();
                       ?>
                          <div class="card-header bg-transparent border-0">
                            <h1 class="error-code text-center mb-2">AKTIVASI AKUN BERHASIL <i class="fa fa-check-circle"></i></h1>
                            <h3 class="text-center">-- Silahkan Login --</h3>
                          </div>
                        <?php
                          } else {
                            redirect(base_url())?>
                        <?php
                          }
                        } else { ?>
                          <div class="card-header bg-transparent border-0">
                            <h1 class="error-code text-center mb-2">403</h1>
                            <h3 class="text-uppercase text-center">Access Denied/Forbidden !</h3>
                          </div>
                        <?php
                        } ?>

                        <div class="card-content">
                          <div class="row py-2">
                            <div class="col-12">

                              <a href="<?= base_url() ?>" class="btn btn-primary btn-block"><i class="ft-home"></i> Back to Home</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </section>

                </div>
              </div>
            </div>

        </div>
      </div>
    </div>
  </div>
  </section>
