<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class libplanning
{
    private $_CI;

    function __construct()
    {
      $this->_CI =& get_instance();
      $this->_CI->load->library('session');
    }

    function sirup($reqId,$sirupId)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("Importsirup");
      $sirup = new Importsirup();
      // $sirup->selectByParamsDetailRUP(array("ID" => $sirupId));
      $sirup->selectByParamsDetailRUP(array("PERMOHONAN_PAKET_ANALISA_ID" => $reqId));

      $sirup->firstRow();

      $reqTahun = $sirup->getField("TAHUN");
      $reqKodeRUP = $sirup->getField("KODE_RUP");
      $reqKodeRUPPermohonan = $sirup->getField("KODE_RUP_PERMOHONAN");
      $reqKodePR = $sirup->getField("KODE_PR");
      $reqKodePRPermohonan = $sirup->getField("KODE_PR_PERMOHONAN");
      $reqKodeSA = $sirup->getField("KODE_SA");
      $reqDPSJ = $sirup->getField("KODE_DPSJ");
      $reqNoUrut = $sirup->getField("NO_URUT");
      $reqKategoriPaketID = $sirup->getField("KATEGORI_PAKET_ID");
      $reqNamaPaket = $sirup->getField("NAMA_PAKET");
      $reqNamaPaketPermohonan = $sirup->getField("NAMA_PAKET_PERMOHONAN");
      $reqWaktuPelaksanaan = $sirup->getField("TANGGAL_WAKTU_PELAKSANAAN");
      $reqLokasiPekerjaan = $sirup->getField("LOKASI_PEKERJAAN");
      $reqJenisKontrak = $sirup->getField("JK_NAME");
      $reqNilaiPagu = $sirup->getField("NILAI_PAGU");
      $reqListKegiatan = $sirup->getField("LIST_KEGIATAN");
      $reqWaktuAwal = $sirup->getField("WAKTU_AWAL");
      $reqWaktuAkhir = $sirup->getField("WAKTU_AKHIR");
      $reqStatusProses = $sirup->getField("STATUS_PROSES");
      $reqName = $sirup->getField("NAME");
      $reqKategoriPaket = $sirup->getField("KATEGORI_PAKET");
      $reqNamaSA = $sirup->getField("NAMA_SA");
      $reqNamaDPSJ = $sirup->getField("NAMA_DPSJ");
      $reqMetodePemilihan = $sirup->getField("METODE_PEMILIHAN");
      $reqNamaJenisPekerjaan = $sirup->getField("NAMA_JENIS_PEKERJAAN");
      $reqHasilVerifikasi = $sirup->getField("HASIL_VERIFIKASI");
      $reqCreatedBy = $sirup->getField("CREATED_BY");
      $reqCreatedAt = $sirup->getField("CREATED_AT");
      $reqUpdatedBy = $sirup->getField("UPDATED_BY");
      $reqUpdatedAt = $sirup->getField("UPDATED_AT");
      $reqImportDate = $sirup->getField("IMPORT_DATE");
      $reqNilaiPaguPR = $sirup->getField("NILAI_RAB_PR");
      if ($sirup->getField("PENGADAAN_BYPASS") == '1') {
        $reqPengadaanBypass = 'Ya';
      } else {
        $reqPengadaanBypass = 'Tidak';
      }
      $reqSumberDana = $sirup->getField("SUMBER_DANA");

      $html  = '';
      // $html .= '
      //           <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
      //             <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>SIRUP</strong>
      //           </div>
      //           <div class="p-1">';
      $html .= '<table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <td width="10%" colspan="2" style="background: #f6db00; color:#000"><b>Tahun</b></td>
                      <td width="90%" colspan="6">'.$reqTahun.'</td> 
                    </tr>
                    <tr>
                      <td width="10%" colspan="2"style="background: #f6db00; color:#000"><b>Kode RUP</b></td>
                      <td width="90%" colspan="6">'.$reqKodeRUPPermohonan.'</td>
                    </tr>
                    <tr>
                      <td width="10%" colspan="2"style="background: #f6db00; color:#000"><b>Kode PR</b></td>
                      <td width="90%" colspan="6">'.$reqKodePRPermohonan.'</td>
                    </tr>
                    <tr>
                      <td width="11%" colspan="2" style="background: #f6db00; color:#000"><b>SA</b></td>
                      <td width="25%" colspan="2">'.$reqKodeSA.' - '.$reqNamaSA.'</td> 
                      <td width="11%" colspan="2" style="background: #f6db00; color:#000"><b>DPSJ</b></td>
                      <td width="25%" colspan="2">
                      '.$reqDPSJ.'
                        <br>';
                          $namaDPSJ = $this->parsePostgresArray2($reqNamaDPSJ);
                          foreach ($namaDPSJ as $key => $value) {
                            $html .=  $value.'<br>';
                          } 
              $html .= '
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Nama Paket</b></td>
                      <td colspan="6">'.$reqNamaPaketPermohonan.'</td>
                    </tr>
                    <tr>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Metode Pemilihan</b></td>
                      <td colspan="2">'.$reqMetodePemilihan.'</td>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Nama Jenis Pekerjaan</b></td>
                      <td colspan="2">'.$reqNamaJenisPekerjaan.'</td>
                    </tr>
                    <tr>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Nilai Pagu RUP</b></td>
                      <td colspan="2">'.currencyToPage($reqNilaiPagu).'</td>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Nilai RAB</b></td>
                      <td colspan="2">'.currencyToPage($reqNilaiPaguPR).'</td>
                    </tr>
                    <tr>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Waktu Awal</b></td>
                      <td colspan="2">'.str_replace('<br>',' ',getFormattedDateYMJson($reqWaktuAwal)).'</td>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Waktu Akhir</b></td>
                      <td colspan="2">'.str_replace('<br>',' ',getFormattedDateYMJson($reqWaktuAkhir)).'</td>
                    </tr>
                    <tr>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Status Proses</b></td>
                      <td colspan="2">'.$reqStatusProses.'</td>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Name</b></td>
                      <td colspan="2">'.$reqName.'</td>
                    </tr>
                    <tr>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Kategori Paket</b></td>
                      <td colspan="2">'.$reqKategoriPaket.'</td> 
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Waktu Pelaksanaan</b></td>
                      <td colspan="2">'.$reqWaktuPelaksanaan.'</td> 
                    </tr>
                    <tr>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Lokasi Pekerjaan</b></td>
                      <td colspan="2">'.$reqLokasiPekerjaan.'</td> 
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Jenis Kontrak</b></td>
                      <td colspan="2">'.$reqJenisKontrak.'</td> 
                    </tr>
                    <tr>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Sumber Dana</b></td>
                      <td colspan="2">'.$reqSumberDana.'</td> 
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Pengadaan Bypass</b></td>
                      <td colspan="2">'.$reqPengadaanBypass.'</td> 
                    </tr>
                  </tbody>
                </table>
              </div>';

      return $html;
    }

    function sirupHeader($reqId,$sirupId)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("Importsirup");
      $sirup = new Importsirup();
      // $sirup->selectByParamsDetailRUP(array("ID" => $sirupId));
      $sirup->selectByParamsDetailRUP(array("PERMOHONAN_PAKET_ANALISA_ID" => $reqId));
      $sirup->firstRow();

      $reqTahun = $sirup->getField("TAHUN");
      $reqKodeRUP = $sirup->getField("KODE_RUP");
      $reqKodeRUPPermohonan = $sirup->getField("KODE_RUP_PERMOHONAN");
      $reqKodePR = $sirup->getField("KODE_PR");
      $reqKodePRPermohonan = $sirup->getField("KODE_PR_PERMOHONAN");
      $reqKodeSA = $sirup->getField("KODE_SA");
      $reqDPSJ = $sirup->getField("KODE_DPSJ");
      $reqNoUrut = $sirup->getField("NO_URUT");
      $reqKategoriPaketID = $sirup->getField("KATEGORI_PAKET_ID");
      $reqNamaPaket = $sirup->getField("NAMA_PAKET");
      $reqNilaiPagu = $sirup->getField("NILAI_PAGU");
      $reqListKegiatan = $sirup->getField("LIST_KEGIATAN");
      $reqWaktuAwal = $sirup->getField("WAKTU_AWAL");
      $reqWaktuAkhir = $sirup->getField("WAKTU_AKHIR");
      $reqStatusProses = $sirup->getField("STATUS_PROSES");
      $reqName = $sirup->getField("NAME");
      $reqKategoriPaket = $sirup->getField("KATEGORI_PAKET");
      $reqNamaSA = $sirup->getField("NAMA_SA");
      $reqNamaDPSJ = $sirup->getField("NAMA_DPSJ");
      $reqMetodePemilihan = $sirup->getField("METODE_PEMILIHAN");
      $reqNamaJenisPekerjaan = $sirup->getField("NAMA_JENIS_PEKERJAAN");
      $reqHasilVerifikasi = $sirup->getField("HASIL_VERIFIKASI");
      $reqCreatedBy = $sirup->getField("CREATED_BY");
      $reqCreatedAt = $sirup->getField("CREATED_AT");
      $reqUpdatedBy = $sirup->getField("UPDATED_BY");
      $reqUpdatedAt = $sirup->getField("UPDATED_AT");
      $reqImportDate = $sirup->getField("IMPORT_DATE");
      $reqNilaiPaguPR = $sirup->getField("NILAI_RAB_PR");

      $html  = '';
      $html .= '<table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <td width="15%" colspan="2" style="background: #f6db00; color:#000"><b>Tahun</b></td>
                      <td width="85%" colspan="6">'.$reqTahun.'</td> 
                    </tr>
                    <tr> 
                      <td width="12%" colspan="2" style="background: #f6db00; color:#000"><b>Kode RUP</b></td>
                      <td width="12%" colspan="6">'.$reqKodeRUPPermohonan.'</td> 
                    </tr>
                    <tr> 
                      <td width="12%" colspan="2" style="background: #f6db00; color:#000"><b>Kode PR</b></td>
                      <td width="12%" colspan="6">'.$reqKodePRPermohonan.'</td>
                    </tr>
                    <tr>
                      <td width="25%" colspan="2" style="background: #f6db00; color:#000"><b>Nama Paket</b></td>
                      <td width="25%" colspan="6">'.$reqNamaPaket.'</td>
                    </tr>
                    <tr>
                      <td width="25%" colspan="2" style="background: #f6db00; color:#000"><b>Metode Pemilihan</b></td>
                      <td width="25%" colspan="2">'.$reqMetodePemilihan.'</td>
                      <td width="25%" colspan="2" style="background: #f6db00; color:#000"><b>Nama Jenis Pekerjaan</b></td>
                      <td width="25%" colspan="2">'.$reqNamaJenisPekerjaan.'</td>
                    </tr>
                    <tr>
                      <td width="25%" colspan="2" style="background: #f6db00; color:#000"><b>Nilai Pagu RUP</b></td>
                      <td width="25%" colspan="2">'.currencyToPage($reqNilaiPagu).'</td>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Nilai RAB</b></td>
                      <td colspan="2">'.currencyToPage($reqNilaiPaguPR).'</td>
                  </tbody>
                </table>
              </div>';

      return $html;
    }

    function permohonanHeader($reqId,$permohonanId,$sirupId)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model(array("Permohonanpaket","Importsirup"));
      $permohonanpaket = new Permohonanpaket();
      $sirup = new Importsirup();
      $permohonanpaket->selectByParams(array("A.PERMOHONAN_PAKET_ID" => $permohonanId));
      $permohonanpaket->firstRow();

      $sirup->selectByParams(array("ID" => $sirupId));
      $sirup->firstRow();

      $reqTahun = $permohonanpaket->getField("TAHUN_ANGGARAN"); 
      $reqKodeRUP = $permohonanpaket->getField("KODE_RUP"); 
      $reqKodePR = $permohonanpaket->getField("KODE_PR"); 
      $reqNama = $permohonanpaket->getField("NAMA"); 
      $reqNilaiRAB = $permohonanpaket->getField("NILAI_RAB_PR"); 
      $reqNilaiHPS = $permohonanpaket->getField("NILAI_HPS_PR"); 
      $reqNilaiPagu = $sirup->getField("NILAI_PAGU"); 

      $html  = '';
      $html .= '<table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <td width="15%" colspan="2" style="background: #f6db00; color:#000"><b>Tahun</b></td>
                      <td width="85%" colspan="6">'.$reqTahun.'</td> 
                    </tr>
                    <tr> 
                      <td width="12%" colspan="2" style="background: #f6db00; color:#000"><b>Kode RUP</b></td>
                      <td width="12%" colspan="6">'.$reqKodeRUP.'</td> 
                    </tr>
                    <tr> 
                      <td width="12%" colspan="2" style="background: #f6db00; color:#000"><b>Kode PR</b></td>
                      <td width="12%" colspan="6">'.$reqKodePR.'</td>
                    </tr>
                    <tr>
                      <td width="25%" colspan="2" style="background: #f6db00; color:#000"><b>Nama Paket</b></td>
                      <td width="25%" colspan="6">'.$reqNama.'</td>
                    </tr> 
                    <tr>
                      <td width="25%" colspan="2" style="background: #f6db00; color:#000"><b>Nilai Pagu RUP</b></td>
                      <td width="25%" colspan="2">'.currencyToPage($reqNilaiPagu).'</td>
                      <td colspan="2" style="background: #f6db00; color:#000"><b>Nilai RAB</b></td>
                      <td colspan="2">'.currencyToPage($reqNilaiRAB).'</td>
                    </tr>
                    <tr>
                      <td width="25%" colspan="2" style="background: #f6db00; color:#000"><b>Nilai HPS</b></td>
                      <td width="25%" colspan="2">'.currencyToPage($reqNilaiHPS).'</td> 
                    </tr>
                  </tbody>
                </table>
              </div>';

      return $html;
    }


    function parsePostgresArray($string) {
        $clean = trim($string, '{}');
        $items = str_getcsv($clean);
        $result = [];
        foreach ($items as $item) {
            $item = trim($item, '"');
            $parts = explode("|", $item);
            $result[] = [
                'kode'  => $parts[0] ?? null,
                'nama'  => $parts[1] ?? null,
                'nilai' => $parts[2] ?? null,
            ];
        }
        return $result;
    }

    function parsePostgresArray2($string) {
    $string = trim($string, '{}');
    preg_match_all('/"([^"]*)"/', $string, $matches);
    return $matches[1]; // array hasil
    }

    function ceStatusPR($sirupId)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("Importsirup");
      $this->_CI->load->library("libapiui");
      $sirup = new Importsirup();
      $libapiui = new libapiui();
      $sirup->selectByParamsDetailRUP(array("ID" => $sirupId));
      $sirup->firstRow();

      $reqTahun = $sirup->getField("TAHUN");
      $reqKodeSA = $sirup->getField("KODE_SA");
      $reqKodePR = $sirup->getField("KODE_PR");

      $dataPR = $libapiui->getPR($reqTahun,$reqKodeSA);

      $hasil = $this->findByField($dataPR, 'PR_NO', $reqKodePR);

       return $hasil['Status Persetujuan'];
    }

    function headerPRAttachment($reqId,$sirupId)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("Importsirup");
      $this->_CI->load->library("libapiui");
      $sirup = new Importsirup();
      $libapiui = new libapiui();
      $sirup->selectByParamsDetailRUP(array("ID" => $sirupId));
      $sirup->firstRow();

      $reqTahun = $sirup->getField("TAHUN");
      $reqKodeSA = $sirup->getField("KODE_SA");
      $reqKodePR = $sirup->getField("KODE_PR");

      $dataPR = $libapiui->getPR($reqTahun,$reqKodeSA);

      $hasil = $this->findByField($dataPR, 'PR_NO', $reqKodePR);

      $html  = '';

      if ($hasil) {

          $html .= '<div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Informasi PR</strong>
                    </div> ';
          $html .= '<table class="table table-bordered table-hover"> 
                      <tbody>';
          foreach ($hasil as $key => $value)
          {
            if ($key != 'REQUISITION_HEADER_ID') {
              $html .= '    <tr>
                              <td width="20%" style="background: #f6db00; color:#000"><b>'.$key.'</b></td>
                              <td>'.$value.'</td>
                            </tr>
                        ';
            }
          }

          $html .= '  </tbody>
                    </table>';
          // $html .= '<div class="alert alert-info">Attachment hanya dapat di Download 1x jika ingin</div>';
        } else {
          $html .= '
            <div class="p-1 alert alert-danger"> Informasi PR tidak ditemukan atau Kode PR masih kosong</div>';
        }

      // $html .= downloadBlobFile('bbb');
       return $html;
    }

    function findByField(array $data, string $field, $value, bool $singleResult = false)
    {
      include_once("functions/date.func.php");
      $hasil = [];
      foreach ($data as $row) {
        if ($row->$field == $value) {
            $hasil['Nomor PR'] = $row->PR_NO;
            // $hasil['SA'] = $row->SA.' - '.$row->SA_DESC;
            // $hasil['Organisasi'] = $row->ORG_ID.' - '.$row->ORG_NAME; 
            $hasil['REQUISITION_HEADER_ID'] = $row->REQUISITION_HEADER_ID;
            $hasil['Deskripsi'] = $row->PR_DESC;
            $hasil['Mata Uang'] = $row->CURRENCY_CODE;
            $hasil['Tanggal Pembuatan'] = getFormattedDate($row->PR_CREATION_DATE);
            $hasil['Status Persetujuan'] = $row->AUTHORIZATION_STATUS;
            $hasil['Total Sebelum Pajak'] = currencyToPage($row->TOTAL_WITHOUT_TAX);
            $hasil['Pajak Dapat Diklaim'] = currencyToPage($row->RECOVERABLE_TAX_TAX);
            $hasil['Pajak Tidak Dapat Diklaim'] = currencyToPage($row->NONRECOVERABLE_TAX);
            $hasil['Total Termasuk Pajak'] = currencyToPage($row->TOTAL_WITH_TAX);
        }
      }
      return $hasil; // hasil berupa array (bisa kosong atau berisi data)
    }

    function headerPermohonanDokumen($permohonanpaketid)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("PermohonanPaketAnalisaFile");

      $permohonan_paket_file = new PermohonanPaketAnalisaFile();
      $permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ANALISA_ID" => $permohonanpaketid));

      if ($permohonan_paket_file->countRow() > 0) { 
      $html  = '';
      $html .= '
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Dokumen Final</strong>
                </div>
                <table class="table table-striped table-hover" id="tbl_bidang">
                  <tbody>
                    <tr class="judul-kolom">
                      <th width="80%">Judul</th>
                      <th style="text-align: center;">E-Sign</th>
                      <th style="text-align: center;">Share</th>
                      <th style="text-align: center;">Dok. Final</th>
                      <th style="text-align: center;">Dok. E-Sign</th>
                     </tr>';
                        while($permohonan_paket_file->nextRow())
                        {
                          $fileTTE = '';
                          if ($permohonan_paket_file->getField("FILE_TTE") == '1') {
                            $fileTTE = '<i class="fa fa-check-square-o"></i>';
                            $style = 'style="background-color:transparent; font-weight:bold"';
                            if ($permohonan_paket_file->getField("ESIGN_STATUS") == 'Selesai') {
                              $totalKirim++;
                              $dokEsign = '<a class="badge badge-primary" href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("ESIGN_PATH_FILE").'" target="new"> <i class="fa fa-download"></i> Download</a>';
                            } else {
                              $totalKirim++;
                              $dokEsign = '<span class="badge badge-danger" style="font-size:10px">Belum di ttd</span>';
                            }
                          } else {
                            $dokEsign = '-';
                            $style = 'style="background-color:#d9dddc"';
                          }

                          $fileShare = '';
                          if ($permohonan_paket_file->getField("FILE_SHARE") == '1') {
                            $fileShare = '<i class="fa fa-check-square-o"></i>';
                          }
      $html .= '
                         <tr>
                             <td>'.$permohonan_paket_file->getField("JUDUL").'</td>
                             <td>'.$fileTTE.'</td>
                             <td>'.$fileShare.'</td>
                             <td style="text-align: center;">';
                              if ($permohonan_paket_file->getField("PATH_FILE")) {
      $html .= '              <a class="badge badge-primary" href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("PATH_FILE").'" target="new"> <i class="fa fa-download"></i> Download</a>';
                              } else { echo "";}
      $html .= '
                            </td>
                            <td style="text-align: center;">
                            '.$dokEsign.'
                            </td>
                        </tr>';
                         }
      $html .= '
                  </tbody>
                </table>
                ';
        }

       return $html;
    }

    function headerPermohonanDokumenUnit($permohonanpaketid)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("PermohonanPaketAnalisaFile");

      $permohonan_paket_file = new PermohonanPaketAnalisaFile();
      $permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ANALISA_ID" => $permohonanpaketid, 'FILE_SHARE' => '1'));

      if ($permohonan_paket_file->countRow() > 0) { 
      $html  = '';
      $html .= '
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Dokumen Final</strong>
                </div>
                <table class="table table-striped table-hover" id="tbl_bidang">
                  <tbody>
                    <tr class="judul-kolom">
                      <th width="80%">Judul</th>
                      <th style="text-align: center;">Dok. Final</th>
                      <th style="text-align: center;">Dok. E-Sign</th>
                     </tr>';
                        while($permohonan_paket_file->nextRow())
                        { 
                          $fileTTE = '';
                          if ($permohonan_paket_file->getField("FILE_TTE") == '1') {
                            $fileTTE = '<i class="fa fa-check-square-o"></i>';
                            $style = 'style="background-color:transparent; font-weight:bold"';
                            if ($permohonan_paket_file->getField("ESIGN_STATUS") == 'Selesai') {
                              $totalKirim++;
                              $dokEsign = '<a class="badge badge-primary" href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("ESIGN_PATH_FILE").'" target="new"> <i class="fa fa-download"></i> Download</a>';
                            } else {
                              $totalKirim++;
                              $dokEsign = '<span class="badge badge-danger" style="font-size:10px">Belum di ttd</span>';
                            }
                          } else {
                            $dokEsign = '-';
                            $style = 'style="background-color:#d9dddc"';
                          }
      $html .= '
                         <tr>
                             <td>'.$permohonan_paket_file->getField("JUDUL").'</td>
                             <td style="text-align: center;">';
                              if ($permohonan_paket_file->getField("PATH_FILE")) {
      $html .= '              <a href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("PATH_FILE").'" class="badge badge-primary" target="new"> <i class="fa fa-download"></i> Download</a>';
                              } else { echo "";}
      $html .= '
                            </td>
                            <td style="text-align: center;">
                            '.$dokEsign.'
                            </td>
                        </tr>';
                         }
      $html .= '
                  </tbody>
                </table>
                ';
        }

       return $html;
    }

    function headerPermohonanDokumenTTE($permohonanpaketid,&$totalKirim)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("PermohonanPaketAnalisaFile");

      $permohonan_paket_file = new PermohonanPaketAnalisaFile();
      $permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ANALISA_ID" => $permohonanpaketid));

      if ($permohonan_paket_file->countRow() > 0) { 
        $totalKirim = 0;
        $html  = '';
        $html .= '
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Dokumen Final</strong>
                </div>
                <table class="table table-striped table-hover" id="tbl_bidang">
                  <tbody>
                    <tr class="judul-kolom">
                      <th width="80%">Judul</th>
                      <th style="text-align: center;">E-Sign</th>
                      <th style="text-align: center;">Share</th>
                      <th style="text-align: center;">Dok. Final</th>
                      <th style="text-align: center;">Dok. E-Sign</th>
                     </tr>';
                        while($permohonan_paket_file->nextRow())
                        {
                          $fileTTE = '';
                          if ($permohonan_paket_file->getField("FILE_TTE") == '1') {
                            $fileTTE = '<i class="fa fa-check-square-o"></i>';
                            $style = 'style="background-color:transparent; font-weight:bold"';
                            if ($permohonan_paket_file->getField("ESIGN_STATUS") == 'Selesai') {
                              $totalKirim++;
                              $dokEsign = '<a class="badge badge-primary" href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("ESIGN_PATH_FILE").'" target="new"> <i class="fa fa-download"></i> Download</a>';
                            } else {
                              $totalKirim++;
                              $dokEsign = '<span class="badge badge-danger" style="font-size:10px">Belum di ttd</span>';
                            }
                          } else {
                            $dokEsign = '-';
                            $style = 'style="background-color:#d9dddc"';
                          }

                          $fileShare = '';
                          if ($permohonan_paket_file->getField("FILE_SHARE") == '1') {
                            $fileShare = '<i class="fa fa-check-square-o"></i>';
                          }

      $html .= '
                         <tr '.$style.'>
                             <td>'.$permohonan_paket_file->getField("JUDUL").'</td>
                             <td>'.$fileTTE.'</td>
                             <td>'.$fileShare.'</td>
                             <td style="text-align: center;">';
                              if ($permohonan_paket_file->getField("PATH_FILE")) {
      $html .= '              <a class="badge badge-primary" href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("PATH_FILE").'" target="new"> <i class="fa fa-download"></i> Download</a>';
                              } else { echo "";}
      $html .= '
                            </td>
                            <td style="text-align: center;">
                            '.$dokEsign.'
                            </td>
                        </tr>';
                         }
      $html .= '
                  </tbody>
                </table>
                ';
        }

       return $html;
    }

    function headerPermohonanDokumenForPJPKPP($permohonanpaketid)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("PermohonanPaketAnalisaFile");

      $permohonan_paket_file = new PermohonanPaketAnalisaFile();
      $permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ANALISA_ID" => $permohonanpaketid, "FILE_SHARE" => '1'));

      if ($permohonan_paket_file->countRow() > 0) { 
      $html  = '';
      $html .= '
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Dokumen Final</strong>
                </div>
                <table class="table table-striped table-hover" id="tbl_bidang">
                  <tbody>
                    <tr class="judul-kolom">
                      <th width="80%">Judul</th>
                      <th style="text-align: center;">Dok. E-Sign / Share</th>
                     </tr>';
                        while($permohonan_paket_file->nextRow())
                        {
                          $fileTTE = '';
                          if ($permohonan_paket_file->getField("FILE_TTE") == '1') {
                            $fileTTE = '<i class="fa fa-check-square-o"></i>';
                            $style = 'style="background-color:transparent; font-weight:bold"';
                            if ($permohonan_paket_file->getField("ESIGN_STATUS") == 'Selesai') {
                              $totalKirim++;
                              $dokEsign = '<a class="badge badge-primary" href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("ESIGN_PATH_FILE").'" target="new"> <i class="fa fa-download"></i> Download</a>';
                            } else {
                              $totalKirim++;
                              $dokEsign = '<span class="badge badge-danger" style="font-size:10px">Belum di ttd</span>';
                            }
                          } else {
                            $dokEsign = '-';
                            if ($permohonan_paket_file->getField("FILE_SHARE") == '1') {
                              $dokEsign = '<a class="badge badge-primary" href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("PATH_FILE").'" target="new"> <i class="fa fa-download"></i> Download</a>';
                            } 
                            $style = 'style="background-color:#d9dddc"';
                          }
      $html .= '
                         <tr>
                             <td>'.$permohonan_paket_file->getField("JUDUL").'</td>
                             <td style="text-align: center;">'.$dokEsign.'</td>
                        </tr>';
                         }
      $html .= '
                  </tbody>
                </table>
                ';
        }

       return $html;
    }

    function headerPermohonanDokumenCount($permohonanpakeanalisatid)
    {
      $this->_CI->load->model("PermohonanPaketAnalisaFile");

      $permohonan_paket_file = new PermohonanPaketAnalisaFile();
      $permohonan_paket_file_2 = new PermohonanPaketAnalisaFile();
      // $permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ANALISA_ID" => $permohonanpakeanalisatid),-1,-1," AND ESIGN_STATUS IS NULL");
      $permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ANALISA_ID" => $permohonanpakeanalisatid));
      if ($permohonan_paket_file->countRow() > 0) {
        $permohonan_paket_file_2->selectByParams(array("PERMOHONAN_PAKET_ANALISA_ID" => $permohonanpakeanalisatid),-1,-1," AND ESIGN_STATUS IS NULL");
        if ($permohonan_paket_file_2->countRow() > 0) {
          return true;
        } else {
          return false;
        }
      } else {
        return true;
      }
    }

    function headerPermohonanDokumenEsign($permohonanpaketid,&$totalKirim)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("PermohonanPaketAnalisaFile");
      $this->_CI->load->library("libapiui");

      $permohonan_paket_file = new PermohonanPaketAnalisaFile();
      $permohonan_paket_file->selectByParams(array("PERMOHONAN_PAKET_ANALISA_ID" => $permohonanpaketid));

      $libapiui = new libapiui();
      if ($permohonan_paket_file->countRow() > 0) { 
      $totalKirim = 0;
      $html  = '';
      $html .= '
                <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Dokumen Final</strong>
                </div>
                <table class="table table-striped table-hover" id="tbl_bidang">
                  <tbody>
                    <tr class="judul-kolom">
                      <th width="80%">Judul</th>
                      <th style="text-align: center;">E-Sign</th>
                      <th style="text-align: center;">Share</th>
                      <th style="text-align: center;">Dok. Final</th>
                      <th style="text-align: center;">Dok. E-Sign</th>
                     </tr>';

                     while($permohonan_paket_file->nextRow())
                        {

                          $cekEsign = $libapiui->postEsignCekStatus($permohonan_paket_file->getField("ESIGN_ID"),$fileName);

                            if ($cekEsign->data->status == 'Selesai') { // Update ke DB
                              $permohonan_paket_fileU = new PermohonanPaketAnalisaFile();
                              $permohonan_paket_fileU->setField('PERMOHONAN_PAKET_ANALISA_FILE_ID', $permohonan_paket_file->getField("PERMOHONAN_PAKET_ANALISA_FILE_ID"));
                              $permohonan_paket_fileU->setField('ESIGN_PATH_FILE', $fileName);
                              $permohonan_paket_fileU->setField('ESIGN_STATUS', $cekEsign->data->status);
                              $permohonan_paket_fileU->setField('UPDATED_BY', $this->_CI->USER_LOGIN_ID);
                              $permohonan_paket_fileU->updateEsign400Close();
                              // code...
                            }

                          $fileTTE = '';
                          if ($permohonan_paket_file->getField("FILE_TTE") == '1') {
                            $fileTTE = '<i class="fa fa-check-square-o"></i>';
                            $style = 'style="background-color:transparent; font-weight:bold"';
                            if ($permohonan_paket_file->getField("ESIGN_STATUS") == 'Selesai') {
                              // $totalKirim++;
                              $dokEsign = '<a class="badge badge-primary" href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("ESIGN_PATH_FILE").'" target="new"> <i class="fa fa-download"></i> Download</a>';
                            } else {
                              $totalKirim++;
                              $dokEsign = '<span class="badge badge-danger" style="font-size:10px">Belum di ttd</span>';
                            }
                          } else {
                            $dokEsign = '-';
                            $style = 'style="background-color:#d9dddc"';
                          }

                          $fileShare = '';
                          if ($permohonan_paket_file->getField("FILE_SHARE") == '1') {
                            $fileShare = '<i class="fa fa-check-square-o"></i>';
                          }

      $html .= '
                         <tr '.$style.'>
                             <td>'.$permohonan_paket_file->getField("JUDUL").'</td>
                             <td>'.$fileTTE.'</td>
                             <td>'.$fileShare.'</td>
                             <td style="text-align: center;">';
                              if ($permohonan_paket_file->getField("PATH_FILE")) {
      $html .= '              <a class="badge badge-primary" href="uploads/permohonan_paket/'.$permohonan_paket_file->getField("PATH_FILE").'" target="new"> <i class="fa fa-download"></i> Download</a>';
                              } else { echo "";}
      $html .= '
                            </td>
                            <td style="text-align: center;">
                            '.$dokEsign.'
                            </td>
                        </tr>';
                         } 
      $html .= '
                  </tbody>
                </table>
                ';
        }

       return $html;
    }

    function getRequisitionHeaderId($sirupId)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("Importsirup");
      $this->_CI->load->library("libapiui");
      $sirup = new Importsirup();
      $libapiui = new libapiui();
      $sirup->selectByParamsDetailRUP(array("ID" => $sirupId));
      $sirup->firstRow();

      $reqTahun = $sirup->getField("TAHUN");
      $reqKodeSA = $sirup->getField("KODE_SA");
      $reqKodePR = $sirup->getField("KODE_PR");

      $dataPR = $libapiui->getPR($reqTahun,$reqKodeSA);

      $hasil = $this->findByField($dataPR, 'PR_NO', $reqKodePR);

      return $hasil['REQUISITION_HEADER_ID'];
    }

    function headerRevisiPermohonan($reqId) // analisaID
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model(array("Permohonanpaketapprovalrevisi","Permohonanpaket"));
      $permohonanpaketapprovalrevisi = new Permohonanpaketapprovalrevisi();
      $permohonanpaket = new Permohonanpaket();

      $permohonanpaket->selectByParams(array("A.PERMOHONAN_PAKET_ANALISA_ID" => $reqId)); 
      $permohonanpaket->firstRow();
      $reqPerId = $permohonanpaket->getField("PERMOHONAN_PAKET_ID");

      $permohonanpaketapprovalrevisi->selectByParams(array("PERMOHONAN_PAKET_ID" => $reqPerId));  
      if ($permohonanpaketapprovalrevisi->countRow() > 0) {

        $html = '<div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Dikembalikan dengan Catatan</strong>
                </div> ';
        $html .= '
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="5%">No</th>
                      <th>Catatan</th>
                      <th width="15%" class="text-center">File</th>
                      <th width="25%">Tanggal</th>
                    </tr>
                  </thead>
                  <tbody>';
              $no=1;
              $permohonanpaketapprovalrevisi->selectByParams(array("PERMOHONAN_PAKET_ID" => $reqPerId));  
              if ($permohonanpaketapprovalrevisi->countRow() > 0) {
                while ($permohonanpaketapprovalrevisi->nextRow()) {
                  $tglApproved = explode(" ", $permohonanpaketapprovalrevisi->getField('CREATED_DATE'));
                  $html .= '<tr>';
                  $html .=    '<td>'.$no.'</td>';
                  $html .=    '<td>'.$permohonanpaketapprovalrevisi->getField('CATATAN').'</td>';
                  if ($permohonanpaketapprovalrevisi->getField('FILE')) {
                  $html .=    '<td class="text-center"><a target="_blank" href="uploads/permohonan/'.$permohonanpaketapprovalrevisi->getField('FILE').'" class="badge badge-primary"><span class="fa fa-download"></span> Download</a></td>';
                  } else {
                  $html .=    '<td class="text-center"><a target="_blank">-</td>';
                  }
                  $html .=    '<td>'.getFormattedDate($tglApproved[0]).' '.$tglApproved[1].'</td>';
                  $html .= '</tr>';
                $no++;
                 } 
              } else {
                $html .= '<tr><td colspan="4" class="text-center">. : : Data belum ada : : .</td></tr>';
              }
        $html .= '
            </tbody>
          </table>';
      } else {
        $html .= '';
      }

      return $html;
    }


    function checklist($reqId,$sirupId)
    {
      $this->_CI->load->model(array("Importsirup","Masterchecklist","Permohonanpaket"));
      $sirup = new Importsirup();
      $sirup->selectByParams(array("ID" => $sirupId));
      $sirup->firstRow();

      $reqMetodePemilihan = $sirup->getField("METODE_PEMILIHAN");
      $reqNamaJenisPekerjaan = $sirup->getField("NAMA_JENIS_PEKERJAAN");

      $permohonanpaket = new Permohonanpaket();
      $permohonanpaket->selectByParams(array("A.PERMOHONAN_PAKET_ANALISA_ID" => $reqId)); 
      $permohonanpaket->firstRow();
      $reqPerId = $permohonanpaket->getField("PERMOHONAN_PAKET_ID");

      $html = '<div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                  <span class="alert-icon"><i class="fa fa-th"></i></span>  <strong>Checklist Kelengkapan</strong>
                </div> ';
        $html .= '
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="5%">No</th>
                      <th>Dokumen Kelengkapan</th>
                      <th width="20%">Checklist</th>
                    </tr>
                  </thead>
                  <tbody>';
                    $masterchecklist = new Masterchecklist();
                    $masterchecklist->selectByParams(array("METODE_PEMILIHAN" => $reqMetodePemilihan, "PAKET_JENIS" => $reqNamaJenisPekerjaan),-1,-1,'','',$reqPerId); 
                    $no=1;
                    $totalWajib = 0;
                    $totalChecked = 0;
                    if ($masterchecklist->countRow() > 0) { 
                      while($masterchecklist->nextRow())   
                      {
                        $wajib = '';
                        $data_wajib = 'no';
                        if ($masterchecklist->getField("WAJIB") == '1') {
                          $wajib = '<sup>*</sup>';
                          $totalWajib++;
                          $data_wajib = 'ya';
                        }

                        if ($masterchecklist->getField("WAJIB") == '1' && $masterchecklist->getField("APPROVED") == '1') {
                          $totalChecked++;
                        }

                        $checked = '';
                        $checked2 = '';
                        if ($masterchecklist->getField("APPROVED") == '1') {
                          $checked = 'checked';
                          $checked2 = '<span class="fa fa-check-square-o"></span> Ya, Lengkap';
                        }

                        $html .= '<tr>';
                        $html .=    '<td>'.$no.'</td>';
                        $html .=    '<td>'.$masterchecklist->getField("NAMA").' '.$wajib.'</td>';
                        $html .=    '<td>'.$checked2.'</td>';
                        $html .= '</tr>';
                      $no++;
                      }
                    } else {
                      $html .= '<tr>';
                      $html .=    '<td colspan="3" class="text-center">. : : Tidak ada data : : .</td>';
                      $html .= '</tr>';
                    }
        $html .= '
            </tbody>
          </table>';
       
      return $html;

    }


}
