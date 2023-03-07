<?php
ini_set('max_execution_time', '0');
ini_set('memory_limit','-1');

/**
 * 
 */
class OBR extends CI_Controller
{



  function login()
  {


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://ebms.obr.gov.bi:9443/ebms_api/login/',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "username":"ws400042558900232",
        "password":"vh7W\'PA,"
      }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    // echo $response;

    $data=json_decode($response,true);
    $token=$data['result']['token'];

    $set_session=array('token'=>$token);

    $this->session->set_userdata($set_session);

    // echo $token;

    return $token;
  }

  
  function addInvoice()
  {


    $token=$this->login();

    // $getInvoice=$this->Model->getRequete("SELECT `ID`, `DATE`, `NUMERO_ABONE`, `NOM`, `ADRESSE`, `NIF`, `CATEGORIE`, `NUMERO_FACTURE`, `MONTANT_HT`, `MONTANT_TVA`, `MONTANT_TTC`, `COMMENTAIRE`, `STATUT` FROM `facture` WHERE STATUT=0");

$getInvoice=$this->Model->getRequete("SELECT* FROM vente_vente vv left join saisie_client sc on vv.ID_CLIENT=sc.ID_CLIENT where  vv.DATE_TIME_VENTE> '2023-03-06' and (select count(ID_VENTE_DETAIL) from vente_detail where ID_VENTE=vv.ID_VENTE and ID_BARCODE not in(select ID_BARCODE from req_barcode where HAVE_FACTURE=0) and ID_PRODUIT not in(select ID_PRODUIT from saisie_produit where AGREE_LOCAL=0))>0 and SIGNATURE_FACTURE=0");

// print_r($getInvoice);exit();

    $tableau_principal=array();
    $facture=array();
$i=1;
$NUM=$this->Model->getRequeteOne("SELECT MAX(OBR_NUM) n FROM vente_vente ");
$curl = curl_init();
    if($token)

      foreach ($getInvoice as $value) 
      {
        

$OBR_NUM=$NUM['n']+$i;
 // $OBR_NUM=1000+$i;
// echo$NUM['n'];
// exit();
        $newDate = date("YmdHis", strtotime($value['DATE_TIME_VENTE'])); 

      // echo $value['DATE']." ".$newDate;
        // echo $value['DATE_TIME_VENTE'];

        $invoice_signature="4000425589/ws400042558900232/".$newDate."/".$OBR_NUM;

        $facture["invoice_number"]= $OBR_NUM;
        $facture["invoice_date"]= $value['DATE_TIME_VENTE'];
        $facture["invoice_type"]= "FN";
        $facture["tp_type"]= 2;
        $facture["tp_name"]= "PHARMACIE SAINT RAPHAEL";
        $facture["tp_TIN"]= "4000425589";
        $facture["tp_trade_number"]= "03071";
        $facture["tp_postal_number"]= "";
        $facture["tp_phone_number"]= "72364573";
        $facture["tp_address_province"]= "BUJUMBURA MAIRIE";
        $facture["tp_address_commune"]= "NTAHANGWA";
        $facture["tp_address_quartier"]= "GIHOSHA";
        $facture["tp_address_avenue"]= "Avenue de l'agriculture";
        $facture["tp_address_number"]= " no 26 ";
        $facture["vat_taxpayer"]= "0";
        $facture["ct_taxpayer"]= "0";//A FORNIR PAR ONATEL
        $facture["tl_taxpayer"]= "0";//A FORNIR PAR ONATEL
        $facture["tp_fiscal_center"]= "DPMC";
        $facture["tp_activity_sector"]= "SANTE";
        $facture["tp_legal_form"]= "PRIVE";
        $facture["payment_type"]= "1";//1 EN ESPECE 3 A CREDIT
        $facture["invoice_currency"]= "BIF";
        $facture["customer_name"]= $value['NOM_CLIENT']?$value['NOM_CLIENT']." ".$value['PRENOM_CLIENT']:'undefined';
        $facture["customer_TIN"]= $value['NIF_CLIENT'];
        $facture["customer_address"]= '';
        $facture["vat_customer_payer"]= "";
        $facture["cancelled_invoice_ref"]= "";
        $facture["Invoice_ref"]= $OBR_NUM;
        $facture["invoice_signature"]= $invoice_signature;
        $facture["invoice_signature_date"]=date('Y-m-d h:i:s');

          // print_r($tableau_principal);exit();

          $items=array();

            $remise_pourcent=$this->Model->getRequeteOne("SELECT POURCENTAGE_REMISE from vente_remise where ID_VENTE=".$value['ID_VENTE']." and ID_ASSURANCE is null");



          $getInvoiceItems=$this->Model->getRequete("SELECT ID_VENTE_DETAIL,vd.ID_PRODUIT,NOM_PRODUIT,count(*) qt,min(PRIX_UNITAIRE) prix from vente_detail vd join saisie_produit sp on vd.ID_PRODUIT=sp.ID_PRODUIT where ID_VENTE=".$value['ID_VENTE']." GROUP BY NOM_PRODUIT,ID_PRODUIT");

          
          foreach ($getInvoiceItems as $detail) 
          {

            // $remise=$this->Model->getOne("vente_remise",array("ID_VENTE"=>$value['ID_VENTE']));
            // if($remise){
            //   $rm=$detail['prix']*
            // }
            $items["item_designation"]= $detail['NOM_PRODUIT'];
            $items["item_quantity"]=$detail['qt'];
            $items["item_price"]= $detail['prix'];
            $items["item_ct"]= 0 ;// TAXE DE CONSOMMATION A IDENTIFIE PAR ONATEL,
            $items["item_tl"]= 0;// PRELEVEMENT FORFAITAIREA IDENTIFIE PAR ONATEL,

            $remise_=$remise_pourcent?$detail['prix']*$remise_pourcent['POURCENTAGE_REMISE']/100:0;

            $items["item_price_nvat"]= (($detail['prix']-$remise_)*$detail['qt'])+$items["item_ct"];
            // $items["vat"]= $items["item_price_nvat"]*18/100;
            $items["vat"]= 0;
            $items["item_price_wvat"]= $items["item_price_nvat"]+$items["vat"];
            $items["item_total_amount"]= $items["item_price_wvat"]+$items["item_tl"];
            $facture["invoice_items"][]=$items;

            $items=array();
          }




      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://ebms.obr.gov.bi:9443/ebms_api/addInvoice/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($facture,JSON_UNESCAPED_SLASHES),
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Authorization: Bearer '.$token
        ),
      ));




      $response = curl_exec($curl);

      $response=json_decode( $response,JSON_UNESCAPED_SLASHES);
    // print_r($response['msg']);exit();

      if($response['success'])
      {
        $this->Model->update('vente_vente',array('ID_VENTE'=>$value['ID_VENTE']),array('OBR_NUM'=>$OBR_NUM,'DATE_TIME_ENVOI_OBR'=>date('Y-m-d h:i:s'),'SIGNATURE_FACTURE'=>$invoice_signature,"ENVOIE"=>0));

            foreach ($getInvoiceItems as $detail1)  {     
                    $this->Model->update('vente_detail',array('ID_VENTE'=>$value['ID_VENTE'],'ID_PRODUIT'=>$detail1['ID_PRODUIT']),array('OBR_ENVOI'=>1,"ENVOIE"=>0));
            }
      }


      $tableau_principal[]=$facture;

      

     


    // echo "<pre>";

    //    print_r(json_encode($facture,JSON_UNESCAPED_SLASHES));

    // echo "</pre>";
      $facture=array();

      print_r( "FACTURE No ".$OBR_NUM.":".$response['msg']."<br>");

      // $this->Model->create('facture_captcha_message',array('MESSAGES'=>$response['msg'],'NUMERO_FACTURE'=>$value['NUMERO_FACTURE']));

$i++;
    }

 curl_close($curl);
  }



  function getInvoice($value="")
  {

    $token=$this->login();

    $curl = curl_init();

    //recuperation de la valeur
    $getInvoice=$this->Model->getOne('vente_vente',array('ID_VENTE'=>$value));

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://ebms.obr.gov.bi:9443/ebms_api/getInvoice/',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "invoice_signature":"'.$getInvoice['SIGNATURE_FACTURE'].'"

      }',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$token,
        'Content-Type: application/json'
      ),
    ));



    $response = curl_exec($curl);
    curl_close($curl);
    echo '<pre>'.$response.'</pre>';

  }




  function cancelInvoice($value="")
  {

    $token=$this->login();
    $curl = curl_init();

    //recuperation de la valeur
    // $cancel=$this->Model->getOne('facture',array('STATUT'=>1,'ID'=>$value));
    $cancel=$this->Model->getOne('vente_vente',array('ID_VENTE'=>$value));

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://ebms.obr.gov.bi:9443/ebms_api/cancelInvoice/',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "invoice_signature":"'.$cancel['SIGNATURE_FACTURE'].'"

      }',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$token,
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

 $response=json_decode( $response,JSON_UNESCAPED_SLASHES);
    // print_r($response['msg']);exit();

      if($response['success'])
     $this->Model->update('vente_detail',array('ID_VENTE'=>$cancel['ID_VENTE']),array('OBR_DELETED'=>1));
    // echo '<pre>'.$response.'</pre>';
  redirect(base_url('OBR/list_OBR'));

    

  }

public function list_OBR($dt=NULL,$dt1=NULL){
$data['dt']=$dt;
$data['date2']=$dt1;
$data['']='';

    $this->load->view("list_OBR_view",$data);
}



        public function get_info()
    {

if($this->input->post("DT1")&&$this->input->post("DT2")){
  $filtre_Date=" DATE_TIME_VENTE>='".$this->input->post("DT1")."' and DATE_TIME_VENTE<='".$this->input->post("DT2")." 23:59:59' and DATE_TIME_ENVOI_OBR is not NULL";
}else $filtre_Date=" DATE_TIME_ENVOI_OBR is not NULL";
// echo $this->input->post("DT2");


$i = 1;
            $query_principal = 'SELECT* FROM vente_vente vv left join saisie_client sc on vv.ID_CLIENT=sc.ID_CLIENT where (select count(ID_VENTE_DETAIL) from vente_detail where ID_VENTE=vv.ID_VENTE and ID_BARCODE not in(select ID_BARCODE from req_barcode where HAVE_FACTURE=0) and ID_PRODUIT not in(select ID_PRODUIT from saisie_produit where AGREE_LOCAL=0))>0 and '.$filtre_Date;

            $var_search = !empty($_POST['search']['value']) ? $_POST['search']['value'] : null;

            $limit = 'LIMIT 0,10';


            if ($_POST['length'] != -1) {
                $limit = 'LIMIT ' . $_POST["start"] . ',' . $_POST["length"];
            }
            $order_by = '';

            $order_column = array( 'DATE_TIME_VENTE');

            $order_by = isset($_POST['order']) ? ' ORDER BY ' . $order_column[$_POST['order']['0']['column']] . '  ' . $_POST['order']['0']['dir'] : ' ORDER BY DATE_TIME_VENTE DESC';

            $search = !empty($_POST['search']['value']) ? ("AND NOM_PRODUIT LIKE'%$var_search%' OR QT LIKE '%$var_search%' OR OBR_NUM LIKE '%$var_search' OR DATE_TIME_VENTE LIKE '%$var_search' OR DATE_TIME_VENTE LIKE '%$var_search' ") : '';

            $critaire = '';

            $query_secondaire = $query_principal . ' ' . $critaire . ' ' . $search . ' ' . $order_by . '   ' . $limit;
            $query_filter = $query_principal . ' ' . $critaire . ' ' . $search;


            $fetch_school = $this->Model->datatable($query_secondaire);
            $data = array();
            $i=1;

            foreach ($fetch_school as $row) {
              $remise_pourcent=$this->Model->getRequeteOne("SELECT POURCENTAGE_REMISE from vente_remise where ID_VENTE=".$row->ID_VENTE." and ID_ASSURANCE is null");

              $getInvoiceItems=$this->Model->getRequete("SELECT ID_VENTE_DETAIL,vd.ID_PRODUIT,NOM_PRODUIT,AGREE_LOCAL,count(*) qt,min(PRIX_UNITAIRE) prix,OBR_DELETED from vente_detail vd join saisie_produit sp on vd.ID_PRODUIT=sp.ID_PRODUIT where ID_VENTE=".$row->ID_VENTE." GROUP BY NOM_PRODUIT,ID_PRODUIT");
              $items='';
              $montant=0;
              foreach ($getInvoiceItems as $key) {
                $remise_=$remise_pourcent?$key['prix']*$remise_pourcent['POURCENTAGE_REMISE']/100:0;
                if($key['AGREE_LOCAL']==0){
                $key['NOM_PRODUIT']="############";
                }
              $items.=$key['NOM_PRODUIT'].', ';
              if($key['AGREE_LOCAL']==1)
              $montant+=($key['prix']-$remise_)*$key['qt'];
              $deleted=$key['OBR_DELETED']==1?"<span style='color:red'>":"<span>";
              }

                $sub_array = array();
                $sub_array[] =$deleted. $i ."</span>";
                $sub_array[] = $deleted.$row->OBR_NUM."</span>" ;
                $sub_array[] = $deleted.$row->DATE_TIME_VENTE."</span>";
                $sub_array[] = $deleted.$row->DATE_TIME_ENVOI_OBR."</span>";
                $sub_array[] = $deleted.$row->NOM_CLIENT." ".$row->PRENOM_CLIENT."</span>";
                $sub_array[] = $deleted.$items."</span>";
                $sub_array[] = $deleted.$montant."</span>";
                $sub_array[]= "<div class='modal fade' id='desactcat".$row->ID_VENTE."' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
                     <div class='modal-dialog modal-sm'>
                       <div class='modal-content'>
                         <div class='modal-header'>
                           <h4 class='modal-title' id='myModalLabel'></h4>
                           <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                             <span aria-hidden='true'>&times;</span>
                           </button>
                         </div>
                         <div class='modal-body'>
                           ".$this->design_facture($row->ID_VENTE)."
                           
                         </div>
                         <div class='modal-footer'>
                           <button type='button' class='btn btn-default' data-dismiss='modal'>Annuler</button>
                           <a href='' class='btn '></a>
                         </div>
                       </div>
                     </div>
                   </div>

                   <div class='modal fade' id='desactcat1".$row->ID_VENTE."' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
                     <div class='modal-dialog modal-sm'>
                       <div class='modal-content'>
                         <div class='modal-header'>
                           <h4 class='modal-title' id='myModalLabel'></h4>
                           <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                             <span aria-hidden='true'>&times;</span>
                           </button>
                         </div>
                         <div class='modal-body'>
                           VOULEZ-VOUS SUPPRIMER CETTE FACTURE?
                           
                         </div>
                         <div class='modal-footer'>
                           <button type='button' class='btn btn-default' data-dismiss='modal'>Annuler</button>
                           <a href='".base_url('OBR/cancelInvoice/').$row->ID_VENTE."' class='btn btn-danger' >Supprimer</a>
                         </div>
                       </div>
                     </div>
                   </div>
                              <a href='#' data-toggle='modal' data-target='#desactcat".$row->ID_VENTE."' class='btn btn-primary' href='".base_url('School_Controller/updating/').$row->ID_VENTE.")'>Detail</a>
                              <a href='#' data-toggle='modal' data-target='#desactcat1".$row->ID_VENTE."' class='btn btn-danger' ><i class='fas fa-trash-alt'></i></a>

                              ";

        $data[] = $sub_array;
        $i++;
    }

    $output = array(
        "draw" => intval($_POST['draw']),
        "recordsTotal" => $this->Model->all_data($query_principal),
        "recordsFiltered" => $this->Model->filtrer($query_filter),
        "data" => $data
    );
    echo json_encode($output);
}


  public function design_facture($id){
  $NUMERO_FATURE=$id;
  $donne_facture = $this->Model->getRequeteOne('SELECT config_user.NOM, config_user.PRENOM, DATE_TIME_VENTE, MONTANT_TOTAL, MONTANT_PAYE, MONTANT_REMISE, saisie_client.NOM_CLIENT, saisie_client.PRENOM_CLIENT FROM `vente_vente` JOIN config_user ON config_user.ID_USER = vente_vente.ID_USER_VENDEUR LEFT JOIN saisie_client ON saisie_client.ID_CLIENT = vente_vente.ID_CLIENT WHERE 1 AND ID_VENTE = "'.$NUMERO_FATURE.'"');

  $det_medicam = $this->Model->getRequete('SELECT DISTINCT(NOM_PRODUIT) AS NOM_PRODUIT,AGREE_LOCAL, COUNT(NOM_PRODUIT) AS QUANTITE, (SUM(PRIX_UNITAIRE) / COUNT(NOM_PRODUIT)) AS PRIX_UNITAIRE,  SUM(PRIX_UNITAIRE) AS PRIX_TOTAL FROM `vente_detail` JOIN saisie_produit ON saisie_produit.ID_PRODUIT=vente_detail.ID_PRODUIT WHERE `ID_VENTE` = '.$NUMERO_FATURE.' GROUP BY NOM_PRODUIT');
  $det_remise = $this->Model->getRequete('SELECT MONTANT_TOTAL, MONTANT_REMISE, POURCENTAGE_REMISE, NOM_ASSURANCE FROM `vente_remise` LEFT JOIN saisie_assurance ON saisie_assurance.ID_ASSURANCE = vente_remise.ID_ASSURANCE WHERE `ID_VENTE` = '.$NUMERO_FATURE.' ');

  $date=date_create($donne_facture['DATE_TIME_VENTE']);
  $DATE_INSERT = date_format($date,"d/m/Y H:i:s");

$info="<center>";

$info.='<div style="font-size:13px"><img src="'.base_url().'dist/straphael_favicon.png"><br>';
$info.='<h3>Pharmacie Saint-Raphaël</h3>';
$info.='26 avenue de l\'agriculture ';
$info.='Bujumbura, Burundi <br>';
$info.='Tel: 72364573 ';
$info.='NIF: 4000425589 ';
$info.='RC: 03071 <br>';
$info.='Date '.$DATE_INSERT.'<br><br>';
$info.="</center>";
$info.='Vente Num/Ref : <b>'.$NUMERO_FATURE.'</b><br>';
$info.='Associer des ventes: <b>'.$donne_facture['NOM'].' '.$donne_facture['PRENOM'].'</b><br>';
$info.='Client : <b>'.$donne_facture['NOM_CLIENT'].' '.$donne_facture['PRENOM_CLIENT'].'</b><br>';
$info.='-------------------------------------------------<br><br>';
$info.='</div><table style="width:100%"><tr><th  align ="left" style="width:10px">#</th><th style="width:25%" align ="left">Px</th><th style="width:20%" align ="right">Qt</th><th style="width:20%" align ="right">PU</th><th style="width:25%" align ="right">PT</th></tr>';

$i = 1;
 $montant=0;
  foreach ($det_medicam as  $key) {
$nom=$key['NOM_PRODUIT'];
$pu=$key['PRIX_UNITAIRE'];
$pt=$key['PRIX_TOTAL']*$key['QUANTITE'];
if($key['AGREE_LOCAL']==0){
$nom="############";
$pu="0";
$pt="0";
}
 $montant+=$pt;
    $info.='<tr>';
    $info.='<td  style="width:10pxstyle="width:20%"x">'.$i .')</td>';
    $info.='<td style="width:25%">'.$nom.'</td>';
    $info.='<td style="width:20%" align ="right">'.$key['QUANTITE'].'</td>';
    $info.='<td style="width:20%" align ="right">'.number_format($pu,0,',',' ').'</td>';
    $info.='<td style="width:25%" align ="right">'.number_format($pt,0,',',' ').'</td>';
    // $info.='# '.$i.' '.$key['NOM_PRODUIT'].' '),0,1,'L');
    // $info.=number_format($key['QUANTITE'],0,',',' ').' pc * '.number_format($key['PRIX_UNITAIRE'],0,',',' ').' BIF'),0,0,'L');
    // $pdf->Cell(90,10,utf8_decode(number_format($key['PRIX_TOTAL'],0,',',' ').' BIF'),0,1,'R');
     $i++;

    $info.='</tr>';
}

$info.='<tr><td colspan="5">-------------------------------------------------<br></td></tr>';
$info.='<tr><th align ="left" colspan="3">TOTAL</th><th align ="right" colspan="2">'.number_format($montant,0,',',' ').'</th></tr>';

foreach ($det_remise as  $rem) {
  if ($rem['NOM_ASSURANCE'] == NULL) {
    
    $info.='<tr><td colspan="3">Remise</td><td  colspan="2" align ="right">'.number_format($rem['MONTANT_REMISE'],0,',',' ').'</td></tr>';

  }
  else{
    
     $info.='<tr><td colspan="3">Assurance ('.$rem['NOM_ASSURANCE'].')</td><td  colspan="2" align ="right">'.number_format($rem['MONTANT_REMISE'],0,',',' ').'</td></tr>';
  }

  $montant-=$rem['MONTANT_REMISE'];
}

$info.='</table><table style="width:100%">';

$info.='<tr><td>Total Remise</td><td align ="right">'.number_format($donne_facture['MONTANT_REMISE'],0,',',' ').'</td>';
$info.='<tr><td>Montant A paye</td><td align ="right"><b>'.number_format($montant,0,',',' ').'</b></td>';


$info.='</table><br>';
$info.='<center>Merci pour votre confiance</center?';


return $info;
  


}

}
