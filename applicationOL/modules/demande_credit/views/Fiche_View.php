<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        
        <title>Fiche</title>
        <?php
        meta_tags();
    ?>
        <!-- Bootstrap CSS -->
        <?php
    $active1="";
    $active2="";
    $active3="";
    $active4="";
    $active5="";
    $active6="";
    $active7="";
    $active8="";
    $active9="";
    $active10="active";
    $active11="";

    ?>
        <?php
    
       include VIEWPATH.'includes/header.php';

    ?>
    <style type="text/css">
      b{color: black}
    </style>
    </head>
    <body>
        
        <!--================Header Menu Area =================-->
        <?php
       include VIEWPATH.'includes/menu_principal.php';
        ?>
    <!-- <div class="container-fluid" style="border-top: 20px solid #B3001B;"> -->
    <div id="presentation" class="container-fluid" >
 <!-- <section class="banner_area">
            <div class="banner_inner d-flex align-items-center">
                <div class="container">
                    <div class="banner_content">
                        <?php
                        $titre="";
                        if ($type==1) {
                           $titre="Fiches de demande de crédits aux groupements des jeunes";
                        }

                        if ($type==2) {
                           $titre="Fiches de demande d’autres crédits";
                        }

                        ?>
                        <h2><?=$titre?></h2>
                        <div class="page_link">
                            <a href="<?=base_url();?>">Accueil</a>
                            <a class="" href="#">Fiches de demande de crédits</a>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
    </div>


        <div class="container" style="margin-bottom:0px; background: white">

        <!-- <?=$breadcrumb?> -->
        <p></p>
    
     <?= $this->session->flashdata('message') ?>
     <?php if($this->session->userdata('PROFIL')){ ?>
             <form method="post" id='myform' action="<?php echo base_url('demande_credit/Fiche/add/')?><?=$type?>"  enctype="multipart/form-data">
                <fieldset class="border p-2"><legend class="w-auto" >Ajout d'un fiche de demande de crédit</legend>

               <div class="container-fluid row" >
                     <div class="col-md-2 sm-12 xs-12 form-group">
                        <label>Type de fiche:</label>
                    </div>
                    <div class="col-md-4 sm-12 xs-12 form-group">
                    
                <input type="text" name="titre" id="titre" class="form-control input-sm" autocomplete="off" required>       
                    </div>
                    <div class="col-md-2 sm-12 xs-12 form-group">
                        <label>Fichier à télécharger:</label>
                    </div>
                    <div class="col-md-4 sm-12 xs-12 form-group">
                    
                <input type="file" name="pj" id="pj" class="form-control input-sm" accept="application/pdf , application/msword" autocomplete="off" required>     
                    </div>
                </div>
                
                <div class="col-md-12 form-group">

                        <input type="submit" name="submit" id="submit"  class="btn btn-light active form-control" value="Enregistrer">    
                    
                    </div>
            </fieldset>
            </form><p>
            <?php } ?>

            <?= $this->table->generate($table); ?>  

</div>
      
     
        
        <!--================ start footer Area  =================-->	
        <?php
        include VIEWPATH.'includes/footer.php';
        ?>
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    </body>
    
</html>
<script>
       $(document).ready(function(){
        // $('#DATE').datetimepicker({format: 'd-m-Y'});

    // var row_count ="1000000";
     // alert(row_count);
$("#mytable").DataTable({
     "order": [[0, "desc" ]],
       
                language: {
                "sProcessing":     "Traitement en cours...",
                "sSearch":         "Rechercher&nbsp;:",
                "sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
                "sInfo":           "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                "sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
                "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                "sInfoPostFix":    "",
                "sLoadingRecords": "Chargement en cours...",
                "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
                "oPaginate": {
                  "sFirst":      "Premier",
                  "sPrevious":   "Pr&eacute;c&eacute;dent",
                  "sNext":       "Suivant",
                  "sLast":       "Dernier"
                },
                "oAria": {
                  "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
                  "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
                }
            }


            }); 

  
}); 
  </script> 
 
