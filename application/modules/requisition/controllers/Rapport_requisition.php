<?php 
 /**
  * 
  */
 class Rapport_requisition extends CI_Controller
 {
   
  function __construct()
  {
    parent::__construct();
    $this->load->library('Mylibrary');
    $this->ci = & get_instance();
    $this->ci->load->library("user_agent");
    $this->Is_Connected();

    }

  public function Is_Connected()
       {

       if (empty($this->session->userdata('STRAPH_ID_USER')))
        {
         redirect(base_url('Login/'));
        }
  }

  public function index($dt=''){

    // RAPPORT REQUISITION

    if($dt){
      $condition=" AND DATE_REQUISITION LIKE '%".$dt."%' ";
    }else $condition='';


 
    $query_pro=$this->Model->getRequete("SELECT* from saisie_produit pro order by NOM_PRODUIT LIMIT 100");
    $query=$this->Model->getRequete("SELECT* from saisie_produit pro where ID_PRODUIT in(SELECT ID_PRODUIT FROM req_requisition WHERE 1  AND ID_SOCIETE=".$this->session->userdata('STRAPH_ID_SOCIETE').$condition.") order by NOM_PRODUIT");

    
    $products="";
    $requisition_m="";
    $requisition_q="";
    $q_entre="";
    $q_n_entre="";

    $montant_t=0;
    $qt_t=0;
    
if(count($query)>0){
  $data['nombre'] =(count($query)*200)+200;
  
  foreach ($query as $key){

    $query_m=$this->Model->getRequeteOne("SELECT sum(MONTANT_TOTAL_ACHAT) as m from req_requisition req where ID_PRODUIT=".$key["ID_PRODUIT"]." AND ID_SOCIETE=".$this->session->userdata('STRAPH_ID_SOCIETE').$condition);
    $query_q=$this->Model->getRequeteOne("SELECT sum(QUANTITE) as q from req_requisition req where ID_PRODUIT=".$key["ID_PRODUIT"]." AND ID_SOCIETE=".$this->session->userdata('STRAPH_ID_SOCIETE').$condition);
    $query_q_entre=$this->Model->getRequeteOne("SELECT count(ID_BARCODE) as q from req_barcode req JOIN req_requisition req1 ON req.ID_REQUISITION=req1.ID_REQUISITION where req.ID_PRODUIT=".$key["ID_PRODUIT"]." AND req.ID_SOCIETE=".$this->session->userdata('STRAPH_ID_SOCIETE').$condition);
    $query_q_non_entre=$query_q['q']-$query_q_entre['q'];
$pro=str_replace("'", "\'", $key['NOM_PRODUIT']);
    // print_r($query_m);
    $products.="'".$pro."',";
    $montant_t+=$query_m['m'];
    $requisition_m.=$query_m['m'].",";
    $requisition_q.=$query_q['q'].",";
    $q_entre.=$query_q_entre['q'].",";
    $q_n_entre.=$query_q_non_entre.",";
    $qt_t+=$query_q['q'];
  }

  $products.="|";
  $requisition_m.="|";
  $requisition_q.="|";
  $q_entre.="|";
  $q_n_entre.="|";



}else{
  $data['nombre'] =(count($query_pro)*30)+200;

  foreach ($query_pro as $key){
    $pro=str_replace("'", "\'", $key['NOM_PRODUIT']);
    $products.="'".$pro."',";

    $requisition_m.="0,";
    $requisition_q.="0,";
    $q_entre.="0,";
    $q_n_entre.="0,";
  }
  $products.="|";
  $requisition_m.="|";
  $requisition_q.="|";
  $q_entre.="|";
  $q_n_entre.="|";

}
// echo $products;
// exit();

$products=str_replace("/","",$products);
$products=str_replace(",|","",$products);
$products=str_replace("|","",$products);
$requisition_m=str_replace(",|","",$requisition_m);
$requisition_q=str_replace(",|","",$requisition_q);
$q_entre=str_replace(",|","",$q_entre);
$q_n_entre=str_replace(",|","",$q_n_entre);

    $data['products'] =$products;
    $data['requisition_m'] =$requisition_m;
    $data['requisition_q'] =$requisition_q;
    $data['q_entre'] =$q_entre;
    $data['q_n_entre'] =$q_n_entre;
    $data['montant_t'] =$montant_t;
    $data['qt_t'] =$qt_t;
    $data['dt'] =$dt;
    // print_r($q_n_entre);
    // echo $products;
    // exit();


    // FIN RAPPORT REQUISITION

    // RAPPORT STOCK

if($dt){
      $condition_req=" AND DATE_REQUISITION = '".$dt."' ";
      $condition_p=" AND DATE_TIME <= '".$dt."' ";
      $condition_v=" AND DATE_TIME_VENTE <= '".$dt."' ";
    }else {
      $condition_req='';
      $condition_p='';
      $condition_v='';
    }

// $resultat=$this->Model->getRequete("SELECT p.*, ((SELECT IFNULL(SUM(QUANTITE),0) from req_requisition req where ID_PRODUIT=p.ID_PRODUIT  ".$condition_req." AND req.ID_SOCIETE=".$this->session->userdata('STRAPH_ID_SOCIETE').")-(SELECT COUNT(ID_VENTE_DETAIL) from vente_vente v join vente_detail vd on v.ID_VENTE=vd.ID_VENTE where ID_PRODUIT=p.ID_PRODUIT ".$condition_v." AND v.ID_SOCIETE=".$this->session->userdata('STRAPH_ID_SOCIETE').")-(SELECT IFNULL(SUM(QUANTITE),0) from req_stock_disparu disp where ID_PRODUIT=p.ID_PRODUIT ".$condition_p."AND disp.ID_SOCIETE=".$this->session->userdata('STRAPH_ID_SOCIETE').")-(SELECT IFNULL(SUM(QUANTITE),0) from req_stock_endomage endo where ID_PRODUIT=p.ID_PRODUIT ".$condition_p." AND endo.ID_SOCIETE=".$this->session->userdata('STRAPH_ID_SOCIETE').")) as NOMBRE  FROM saisie_produit p WHERE 1 ORDER BY NOM_PRODUIT Limit 2000");


$resultat=$this->Model->getRequete("SELECT p.*, (SELECT IFNULL(SUM(MONTANT_TOTAL_ACHAT),0) from req_requisition req where ID_FOURNISSEUR=p.ID_FOURNISSEUR  ".$condition_req." AND req.ID_SOCIETE=".$this->session->userdata('STRAPH_ID_SOCIETE').") as NOMBRE  FROM saisie_fournisseur p WHERE 1 ORDER BY NOM");



// print_r($resultat); exit();
$infos='';

foreach ($resultat as $value) {
  $pro=str_replace("'", "\'", $value['NOM']);
  if($value['NOMBRE']>0){
    $value['NOMBRE']=$value['NOMBRE'];
  }else{$value['NOMBRE']=0;}
  $infos.='{name: "'.$pro.'",y:'.$value['NOMBRE'].', drilldown: "'.$pro.'"},';
  // $infos.='{name: "jj",y:1, drilldown: "kkk"},';

  // if($value['NOMBRE']!=0)echo $value['NOMBRE'];  exit(); 
}
$infos.="|";

$data['infos'] =str_replace(",|", "", $infos);
$data['num_pro'] =(count($resultat)*30)+200;;

// echo $infos;  exit();  
        $this->load->view('Rapport_requisition_views',$data);
  }

}