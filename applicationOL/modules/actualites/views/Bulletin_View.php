<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        
        <title>Bulletin electronique</title>
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
    $active7="active";
    $active8="";
    $active9="";
    $active10="";
    $active11="";

    ?>
        <?php
    
       include VIEWPATH.'includes/header.php';

    ?>
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
                        <h2>Bulletins électroniques</h2>
                        <div class="page_link">
                            <a href="<?=base_url();?>">Accueil</a>
                            <a class="" href="<?=base_url()?>rapport/Annuel">Bulletins</a>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <div class="container" style="margin-bottom:0px; background: white">

        <!-- <?=$breadcrumb?> -->
        <p></p>
    
     <?= $this->session->flashdata('message') ?>
     <?php if($this->session->userdata('PROFIL')){ ?>
             <form method="post" id='myform' action="<?php echo base_url('actualites/Bulletin/add')?>"  enctype="multipart/form-data">
                <fieldset class="border p-2"><legend class="w-auto" >Envoie d'un bulletin électronique</legend>

               <div class="container-fluid row" >
                      
                       <div class="col-md-8 sm-12 xs-12 form-group">
                <label>Objet:</label>
                     <input type="text" name="titre" id="titre" class="form-control input-sm" autocomplete="off" required>
                     </div>
                    <div class="col-md-8 sm-12 xs-12 form-group">
                <label>Wessage:</label>
                    <textarea name="msg" id="msg" class="form-control input-sm" autocomplete="off" required></textarea>
                      
                    </div>
                    <div class="col-md-8 sm-12 xs-12 form-group">
                    <label>Pièce jointe</label>
                     <input type="file" name="pj" id="pj" class="form-control input-sm" autocomplete="off" style="display: ;"> 
        
                    </div>
                    
                </div>
                
                <div class="col-md-12 form-group">

                        <input type="submit" name="submit" id="submit"  class="btn btn-light active form-control" value="Enregistrer">    
                    
                    </div>
            </fieldset>
            </form><p>
            <?php } ?>

            <?= $this->session->flashdata('message') ?>

            <?= $this->table->generate($table); ?>  

</div>
    </div>
      
     
        
        <!--================ start footer Area  =================-->    
        <?php
        include VIEWPATH.'includes/footer.php';
        ?>
    </body>
</html>
<script>
       $(document).ready(function(){
        // $('#DATE').datetimepicker({format: 'd-m-Y'});

    // var row_count ="1000000";
     // alert(row_count);
$("#mytable").DataTable({
     // "order": [[0, "desc" ]],
//         dom: 'Bfrtlip',
//         buttons: [
//              {extend: 'print', title: 'LISTE DES RAPPORTS <?=date("d-m-Y")?>'},
//                 {extend: 'excel', title: 'LISTE DES RAPPORTS DU <?=date("d-m-Y")?>'},
//                 {extend: 'pdf',orientation: 'landscape', title: 'LISTE DES RAPPORTS DU <?=date("d-m-Y")?>',exportOptions: {
//    // columns: ':visible:not(:eq(4))' 
// },
//     customize: function ( doc ) {
      
//        var colCount = new Array();
//             $('#mytable').find('tbody tr:first-child td').each(function(){
//                 if($(this).attr('colspan')){
//                     for(var i=1;i<=$(this).attr('colspan');i++){
//                         colCount.push('*');
//                     }
//                 }else{ colCount.push('*'); }
//             });
//             doc.content[1].table.widths = colCount;
//     }
// }
//         ],
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