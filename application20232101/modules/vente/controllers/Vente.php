<?php 
 /**
  * 
  */
 class Vente extends CI_Controller
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

          public function Is_permis()
       {

       // if ($this->mylibrary->get_permission('Mettre_Carburant') ==0)
       //  {
       //   redirect(base_url('Login/'));
       //  }
       }






  public function index()
  {
   

     $data['CUNIQUE']=$this->notifications->generate_UIID(13);
     $data['title'] = "Vente ";
     $data['produit']='';
     $data['client'] = $this->Model->getRequete('SELECT * FROM `saisie_client` WHERE ID_SOCIETE LIKE "'.$this->session->userdata('STRAPH_ID_SOCIETE').'" ORDER BY NOM_CLIENT');
     $data['assurance'] = $this->Model->getRequete('SELECT * FROM `saisie_assurance` WHERE ID_SOCIETE LIKE "'.$this->session->userdata('STRAPH_ID_SOCIETE').'" ORDER BY NOM_ASSURANCE');
     $data['remise'] = $this->Model->getRequete('SELECT * FROM `saisie_type_remise` WHERE ID_SOCIETE LIKE "'.$this->session->userdata('STRAPH_ID_SOCIETE').'" AND ID_ASSURANCE = 0 ORDER BY POURCENTAGE');
     $data['totvente']=0;




    $this->load->view("Vente_Add_View",$data);
  }


  public function save_tempovente()
  {
     $BARCODE =$this->input->post('BARCODE');
     $CUNIQUE =$this->input->post('CUNIQUE');

     $bar_produit = $this->Model->getOne('req_barcode',array('BARCODE'=>$BARCODE));

     if (empty($bar_produit)) {
        
    //  // print_r($data_qr);
     $message = "<div class='alert alert-danger' role='alert' id='message'>
                        Barre code non-enregistrer dans le système, veuillez revérifier
                 </div>";       
        $this->session->set_flashdata(array('message'=>$message));

     }
     else{
        if ($bar_produit['STATUS'] == 2) {
            $message = "<div class='alert alert-danger' role='alert' id='message'>
                        Barre code déjà scannée et sortie du stock. Veuillez chercher un autre produit
                 </div>";       
        $this->session->set_flashdata(array('message'=>$message));

        }
        else{
            $new_ver_produit = $this->Model->getOne('vente_detail',array('ID_BARCODE'=>$bar_produit['ID_BARCODE'],'CUNIQUE'=>$CUNIQUE));
            if (!empty($new_ver_produit)) {
                // echo "Vous venez de scanner cette barre code. Veuillez chercher un autre produit pour le prochain scan";
                $message = "<div class='alert alert-danger' role='alert' id='message'>
                        Vous venez de scanner cette barre code. Veuillez chercher un autre produit pour le prochain scan
                 </div>";       
        $this->session->set_flashdata(array('message'=>$message));
               
            }
            else{
                // echo "Produit ajoute avec succès";

                $message = "<div class='alert alert-success' role='alert' id='message'>
                        Produit ajoute avec succès
                 </div>";       
        $this->session->set_flashdata(array('message'=>$message));
                $data_qr = array(
               'ID_VENTE'=>0,                                   
               'ID_BARCODE'=>$bar_produit['ID_BARCODE'],                                   
               'ID_PRODUIT'=>$bar_produit['ID_PRODUIT'],    
               'PRIX_UNITAIRE'=>$bar_produit['PRIX_VENTE'], 
               'ID_SOCIETE'=>$this->session->userdata('STRAPH_ID_SOCIETE'),                              
               'CUNIQUE'=>$this->input->post('CUNIQUE'),               
              );

     $this->Model->create('vente_detail',$data_qr);
     
            }

        }
        // echo "produit bien existant";
     }

// exit();
$data['CUNIQUE']=$this->input->post('CUNIQUE');

     $bar_produitlist = $this->Model->getRequete('SELECT saisie_produit.NOM_PRODUIT,COUNT(*) AS NOMBRE, vente_detail.PRIX_UNITAIRE FROM `vente_detail` JOIN saisie_produit ON saisie_produit.ID_PRODUIT = vente_detail.ID_PRODUIT WHERE 1 AND CUNIQUE like "'.$CUNIQUE.'" GROUP BY saisie_produit.NOM_PRODUIT, PRIX_UNITAIRE ');

     $data['title'] = "Vente ";
     $produit = '<table class="table">';
     $produit .= '<tr><td colspan="4" class="text-center">Produit enregistre</td></tr>';
     $produit .= '<tr><td>Produit</td><td>Quantite</td><td>PU</td><td>PT</td></tr>';
     $tot = 0;
     foreach ($bar_produitlist as $key) {

        $sub_tot = $key['NOMBRE'] * $key['PRIX_UNITAIRE'];
        $tot +=$sub_tot;
        $produit .= '<tr><td>'.$key['NOM_PRODUIT'].'</td><td class="text-right">'.$key['NOMBRE'].'</td><td  class="text-right">'.$key['PRIX_UNITAIRE'].'</td><td  class="text-right">'.$sub_tot.'</td></tr>';
     }
     $produit .= '<tr><td colspan="3">Total</td><td class="text-right">'.$tot.'</td></tr>';
     $produit.='</table>';

     $data['produit'] = $produit;
     $data['totvente']=$tot;
     // echo $tot;
     // exit();
     $data['client'] = $this->Model->getRequete('SELECT * FROM `saisie_client` WHERE ID_SOCIETE LIKE "'.$this->session->userdata('STRAPH_ID_SOCIETE').'" ORDER BY NOM_CLIENT');
     $data['assurance'] = $this->Model->getRequete('SELECT * FROM `saisie_assurance` WHERE ID_SOCIETE LIKE "'.$this->session->userdata('STRAPH_ID_SOCIETE').'" ORDER BY NOM_ASSURANCE');
     $data['remise'] = $this->Model->getRequete('SELECT * FROM `saisie_type_remise` WHERE ID_SOCIETE LIKE "'.$this->session->userdata('STRAPH_ID_SOCIETE').'" AND ID_ASSURANCE = 0 ORDER BY POURCENTAGE');

    $this->load->view("Vente_Add_View",$data);

     





   
  }


  

 public function getassurance()
    {
    $commune= $this->Model->getList("saisie_type_remise",array('ID_ASSURANCE'=>$this->input->post('ID_ASSURANCE')));
    $datas= '<option value="">-- Sélectionner --</option>';
    foreach($commune as $commun){
    $datas.= '<option value="'.$commun["ID_TYPE_REMISE"].'">'.$commun["POURCENTAGE"].'</option>';
    }
    $datas.= '';
    echo $datas;
    }

public function getremiseassurance()
    {
    $remise= $this->Model->getOne("saisie_type_remise",array('ID_TYPE_REMISE'=>$this->input->post('ID_TYPE_REMISE_ASS')));
    
    echo $remise['POURCENTAGE'];
    }

    public function getremiseclient()
    {
    $remise= $this->Model->getOne("saisie_type_remise",array('ID_TYPE_REMISE'=>$this->input->post('ID_TYPE_REMISE_CLIENT')));
    
    echo $remise['POURCENTAGE'];
    }

    

    

  public function save_vente()
  {
     $CUNIQUE =$this->input->post('CUNIQUE');
     $ID_ASSURANCE =$this->input->post('ID_ASSURANCE');
     $ID_CLIENT =$this->input->post('ID_CLIENT');
     $ID_TYPE_REMISE_ASS =$this->input->post('ID_TYPE_REMISE_ASS');
     $ID_TYPE_REMISE_CLIENT =$this->input->post('ID_TYPE_REMISE_CLIENT');
     $MONTANT_TOTAL =$this->input->post('MONTANT_TOTAL');
     $MONTANT_REMISE =$this->input->post('MONTANT_REMISE');
     $MONTANT_ASSURANCE =$this->input->post('MONTANT_ASSURANCE');
     $MONTANT_PAYE =$this->input->post('MONTANT_PAYE');


     $data_vente= array(
                  'ID_USER_VENDEUR'=>$this->session->userdata('STRAPH_ID_USER'),  
                  'MONTANT_TOTAL'=>$MONTANT_TOTAL,  
                  'MONTANT_PAYE'=>$MONTANT_PAYE,  
                  'MONTANT_REMISE'=>$MONTANT_REMISE + $MONTANT_ASSURANCE,  
                  'ID_CLIENT'=>$ID_CLIENT,  
                  'ID_SOCIETE'=>$this->session->userdata('STRAPH_ID_SOCIETE'),
                  );

     // echo "<pre>";
     // print_r($data_vente);

     $ID_VENTE = $this->Model->insert_last_id('vente_vente',$data_vente);

     $det_produit = $this->Model->getRequete('SELECT ID_VENTE_DETAIL, ID_BARCODE FROM `vente_detail` WHERE 1 AND CUNIQUE like "'.$CUNIQUE.'" AND ID_VENTE = 0');
     foreach ($det_produit as $value) {

         $dataupdate_v_detail = array('ID_VENTE' => $ID_VENTE);
         $critere_v_detail = array('ID_VENTE_DETAIL' => $value['ID_VENTE_DETAIL']);
         // print_r($dataupdate_v_detail);
         // print_r($critere_v_detail);


         
         $dataupdate_bar_detail = array('STATUS' => 2);
         $critere_bar_detail = array('ID_BARCODE' => $value['ID_BARCODE']);

         // print_r($dataupdate_bar_detail);
         // print_r($critere_bar_detail);

         $this->Model->update('vente_detail', $critere_v_detail, $dataupdate_v_detail);
         $this->Model->update('req_barcode', $critere_bar_detail, $dataupdate_bar_detail);

         $barcode = $this->Model->getRequeteOne('SELECT ID_PRODUIT, PRIX_VENTE FROM `req_barcode` WHERE 1 AND ID_BARCODE ='.$value['ID_BARCODE'].' ');
         $stock = $this->Model->getRequeteOne('SELECT ID_STOCK, QUANTITE FROM `req_stock` WHERE 1 AND STATUS = 1 AND ID_PRODUIT = '.$barcode['ID_PRODUIT'].' AND PRIX_VENTE = '.$barcode['PRIX_VENTE'].' ');


         $this->Model->update('req_stock', array('ID_STOCK'=>$stock['ID_STOCK']), array('QUANTITE'=>$stock['QUANTITE']-1));


     }

     
     
     
     if ($MONTANT_REMISE > 0) {
         $remise_client= $this->Model->getOne("saisie_type_remise",array('ID_TYPE_REMISE'=>$ID_TYPE_REMISE_CLIENT));
     $MONTANT_TOTAL_CL =  ($MONTANT_REMISE * 100 )/$remise_client['POURCENTAGE'];
   

     $data_remise_client= array(
                  'ID_SOCIETE'=>$this->session->userdata('STRAPH_ID_SOCIETE'),
                  'ENVOIE'=>0,
                  'ID_VENTE'=>$ID_VENTE,
                  'ID_REMISE'=>$ID_TYPE_REMISE_CLIENT,
                  'MONTANT_REMISE'=>$MONTANT_REMISE,
                  'MONTANT_TOTAL'=>$MONTANT_TOTAL_CL,
                  'POURCENTAGE_REMISE'=>$remise_client['POURCENTAGE'],
                  );
     $this->Model->create('vente_remise',$data_remise_client);
     // print_r($data_remise_client);

     }
     
     
     
    


     if ($MONTANT_ASSURANCE > 0) {
         $remise_ass= $this->Model->getOne("saisie_type_remise",array('ID_TYPE_REMISE'=>$ID_TYPE_REMISE_ASS));
     $MONTANT_TOTAL_ASS =  ($MONTANT_ASSURANCE * 100 )/$remise_ass['POURCENTAGE'];
   
     $data_remise_ass= array(
                  'ID_SOCIETE'=>$this->session->userdata('STRAPH_ID_SOCIETE'),
                  'ENVOIE'=>0,
                  'ID_VENTE'=>$ID_VENTE,
                  'ID_REMISE'=>$ID_TYPE_REMISE_ASS,
                  'MONTANT_REMISE'=>$MONTANT_ASSURANCE,
                  'MONTANT_TOTAL'=>$MONTANT_TOTAL_ASS,
                  'ID_ASSURANCE'=>$ID_ASSURANCE,
                  'POURCENTAGE_REMISE'=>$remise_ass['POURCENTAGE'],
                  );
     $this->Model->create('vente_remise',$data_remise_ass);
     // print_r($data_remise_ass);

     }


// $message = "<div class='row alert alert-success' role='alert' id='message'>
//                         <div class='col-md-6'>
//                         Enregistrement fait avec succès.
//                         </div>
//                         <div class='col-md-6 text-right'>
//                         <a class='btn btn-success btn-sm' href='".base_url('vente/Pdf/print_facture/'.$ID_VENTE.'')."' target='_blank' role='button'><i class='fa fa-print' aria-hidden='true'></i> Imprimer la facture de ".$MONTANT_TOTAL." </a>
//                         </div>
//             </div>";    


$message = "<div class='row alert alert-success' role='alert' id='message'>
                        <div class='col-md-6'>
                        Enregistrement fait avec succès.
                        </div>
                        <div class='col-md-6 text-right'>
                        <button id='facture-".$ID_VENTE."' class='btn btn-success btn-sm facture'  role='button'><i class='fa fa-print' aria-hidden='true'></i> Imprimer la facture de ".$MONTANT_TOTAL." </button>
                        </div>
            </div>";    
        $this->session->set_flashdata(array('message'=>$message));
        redirect(base_url('vente/Vente'));

     

     // ;
     // if ($exist) {
     //    echo "exist";
     //    redirect(base_url('requisition/Entree_Stock/scan/'.$this->input->post('ID_REQUISITION').'/'.$this->input->post('ID_PRODUIT')));
     // }
     // else{
     //    echo "not exist";
     //    $this->Model->create('req_barcode',$data_qr);
     //    redirect(base_url('requisition/Entree_Stock/scan/'.$this->input->post('ID_REQUISITION').'/'.$this->input->post('ID_PRODUIT')));
     // }
  }


 }


?>