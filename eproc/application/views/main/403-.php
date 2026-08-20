<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
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
                      <div class="card-header bg-transparent border-0">
                          <h1 class="error-code text-center mb-2">403</h1>
                          <h3 class="text-uppercase text-center">Access Denied/Forbidden !</h3>
                      </div>
                        <div class="card-content"> 
                          <div class="row py-2">
                            <div class="col-12">
                              <?php 
                              $indicesServer = array(
                                                      'REQUEST_TIME' => 'REQUEST TIME', 
                                                      'HTTP_USER_AGENT' => 'BROWSER', 
                                                      'REMOTE_ADDR' => 'IP', 
                                                      ) ; 

                                                      $html = '<table class="table table-bordered">' ; 
                                                      $no = 0;
                                                      foreach ($indicesServer as $arg => $value) { 
                                                        if ($no==0) {
                                                              $html .= '<tr><td>DATE</td><td>' . date('d-m-Y H:i:s') . '</td></tr>' ; 
                                                        }
                                                          if (isset($_SERVER[$arg])) { 
                                                              $html .= '<tr><td>'.$value.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ; 
                                                          } 
                                                          else { 
                                                              $html .= '<tr><td>'.$value.'</td><td>-</td></tr>' ; 
                                                          } 
                                                        $no++;
                                                      } 
                                                      $html .= '</table>' ;  
                                                      echo $html;
                                ?>
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