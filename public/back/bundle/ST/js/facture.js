function showClient(){
    var selectBox = document.getElementById("client");
    var selectedClient = selectBox.options[selectBox.selectedIndex].value;
    if(selectedClient == "---"){
        document.getElementById("rs").value = "";
        document.getElementById("adr").value="";
        document.getElementById("tel").value = "";
        document.getElementById("fax").value = "";
        document.getElementById("em").value = "";
        document.getElementById("actionClient").value = "new";
    }
    else{
        var info = selectedClient.split("|");
        document.getElementById("rs").value = info[0];
        document.getElementById("adr").value = info[1];
        document.getElementById("tel").value = info[2];
        document.getElementById("fax").value = info[3];
        document.getElementById("em").value = info[4];
        var type = document.getElementById("type");

        if (info[5]=="type1")
        {
            type.options[0].selected = true;
        }
        if (info[5]=="type2")
        {
            type.options[1].selected = true;
        }
        if (info[5]=="type3")
        {
            type.options[2].selected = true;
        }
        document.getElementById("actionClient").value = info[6];
    }
}

var idRowTa = 1;
function addRow(){
    var table = document.getElementById("articles");
    var row = document.createElement('tr');
    row.className = "detail";
    row.setAttribute("id", "row-N"+idRowTa);
    var cell1 = document.createElement('td');
    var cell2 = document.createElement('td');
    var cell3 = document.createElement('td');
    var cell4 = document.createElement('td');
    var cell5 = document.createElement('td');
    var cell6 = document.createElement('td');
    var cell1Inner = "<input list='prod-list-N"+idRowTa+"' name='desc[]' type='text' id='row-cellDesc-N"+idRowTa+"' onChange =getInfosService('N"+idRowTa+"')>" +
        "<input type='hidden' name='idProd[]' id='idProd-N"+idRowTa+"'>"+
        "<input type='hidden' name='refProd[]' id='ref-N"+idRowTa+"'>"+
        "<input type='hidden' name='idDet[]' value='0' id='idDet-N"+idRowTa+"'>"+
        "<datalist id='prod-list-N"+idRowTa+"'>";
    json_object = JSON.parse(listOfServices);
    for(var i=0;i<json_object.length;i++){
        cell1Inner = cell1Inner + "<option id='"+json_object[i].prixht+"|"+json_object[i].prixtva+"|"+json_object[i].id+"|"+json_object[i].reference +"|"+json_object[i].qte+"' value='"+json_object[i].titre+"'>"+json_object[i].titre+"</option>";
    }
    cell1Inner = cell1Inner + "</datalist>";
    cell1.innerHTML = cell1Inner;
    row.appendChild(cell1);
    cell2.innerHTML = "<input type='text' name='puht[]' id='puht-N"+idRowTa+"' value='0.00' onchange=calculRow('N"+idRowTa+"') class='puht'>";
    row.appendChild(cell2);
    cell3.innerHTML = "<input type='number' name='qte[]' id='qte-N"+idRowTa+"' min='1' class='numberStyle' value='1' onchange=calculRow('N"+idRowTa+"')>";
    row.appendChild(cell3);
    cell4.innerHTML = "<input type='text' name='ptht[]' id='ptht-N"+idRowTa+"' value='0.00' readonly class='ptht'>";
    row.appendChild(cell4);
    cell5.innerHTML = "<input type='text' name='tva[]' id='tva-N"+idRowTa+"' value='0 %'class='tva' onchange='calulAll()'>";
    row.appendChild(cell5);
    cell6.innerHTML = '<div class="delete"><i class="fa fa-trash" onclick=removeRow("N'+idRowTa+'","")></i></div>';
    row.appendChild(cell6);
    document.getElementById('articles').appendChild(row);
    idRowTa++;

}

var montants = new Array();

function getInfosService(idList){
    var g=$('#row-cellDesc-'+idList).val();
    var id = $('#prod-list-'+idList).find('option').filter(function() { return $.trim( $(this).text() ) === g; }).attr('id');

    if(id != null && typeof id != 'undefined' && id != ""){
        var info = id.split("|");
        document.getElementById("puht-"+idList).value = parseFloat(info[0]).toFixed(2);
        document.getElementById("tva-"+idList).value = info[1]+" %";
        document.getElementById("idProd-"+idList).value = info[2];
        document.getElementById("ref-"+idList).value = info[3];
        document.getElementById("qte-"+idList).setAttribute("max", info[4]);
        var qte = parseInt(document.getElementById("qte-"+idList).value);
        if(qte > info[4]){
            qte = info[4];
            document.getElementById("qte-"+idList).value = qte;
        }
        document.getElementById("ptht-"+idList).value = parseFloat(qte * info[0]).toFixed(2);
        montants["puht-"+idList] = parseFloat(info[0]).toFixed(2);
    }
    else{

        document.getElementById("puht-"+idList).value = "0.00";
        document.getElementById("tva-"+idList).value = "0 %";
        document.getElementById("ptht-"+idList).value = "0.00";
        document.getElementById("idProd-"+idList).value = 0;
        document.getElementById("ref-"+idList).value = "#Ref"+idList;
        montants["puht-"+idList] = 0;
    }
    calulAll();
}

function removeRow(id,from){
    if(from == "update"){
        if(document.getElementById("idDet-"+id).value != 0){
            deleteDetail(id);
        }
        else{
            document.getElementById("row-"+id).innerHTML = "";
        }
    }
    else{
        document.getElementById("row-"+id).innerHTML = "";
    }

    calulAll();
}

function calculRow(id){
    var max = document.getElementById("qte-"+id).getAttribute("max");
    if(parseInt(document.getElementById("qte-"+id).value) > max && typeof max != 'undefined' && max != null){
        document.getElementById("qte-"+id).value = max;
    }
    var puht = parseFloat(document.getElementById("puht-"+id).value.replace(",", ".")) ;
    var qte = parseFloat(document.getElementById("qte-"+id).value);
    document.getElementById("ptht-"+id).value = (qte * puht).toFixed(2);
    calulAll();
}

function calulAll(){

    var somme = 0;
    $("#articles .ptht").each(function() {
        var t = parseFloat($(this).val());
        somme = somme + t;
    });
    document.getElementById("totalHT").innerHTML = lisibilite_nombre(somme.toFixed(2));

    //---------- Calcul de la tva
    var i=0;
    var tva = Array();
    $("#articles .tva").each(function() {
        tva[i] = parseFloat($(this).val().replace(",", "."));
        i++;
    });
    var newTva = tvaOccurence(tva);
    var result = "";
    var someTVA = 0;
    /*var selectBox = document.getElementById("monnaie");
    var selected = selectBox.options[selectBox.selectedIndex].value;*/
    var selected = "DH";

    for(i=0;i<newTva[0].length;i++){
        var some = 0;
        $("#articles .tva").each(function() {
            if(parseFloat($(this).val().replace(",", ".")) == newTva[0][i]){
                var idT = $(this).attr("id");
                var nId = idT.split("-");
                some = some + parseFloat(document.getElementById("ptht-"+nId[1]).value);
            }
        });
        var res = parseFloat(some * (newTva[0][i]/100)).toFixed(2);
        someTVA = parseFloat(someTVA) + parseFloat(res);
        result = result+"<span><b>Total TVA "+newTva[0][i]+" % :</b> "+lisibilite_nombre(res)+"</span> <span id='monTv'>"+selected+"</span><br/>"
    }
    document.getElementById("totalTvaF").innerHTML = result;
    var ttc = parseFloat(someTVA+somme).toFixed(2);
    document.getElementById("totalTTCS").innerHTML = lisibilite_nombre(parseFloat(ttc).toFixed(2));
    document.getElementById("totalTTC").value = parseFloat(ttc).toFixed(2);
}

function tvaOccurence(arr) {
    var a = [], b = [], prev;

    arr.sort();
    for ( var i = 0; i < arr.length; i++ ) {
        if ( arr[i] !== prev ) {
            a.push(arr[i]);
            b.push(1);
        } else {
            b[b.length-1]++;
        }
        prev = arr[i];
    }

    return [a, b];
}

function lisibilite_nombre(nombre)
{
    nombre += '';
    var sep = ' ';
    var reg = /(\d+)(\d{3})/;
    while( reg.test( nombre)) {
        nombre = nombre.replace( reg, '$1' +sep +'$2');
    }
    return nombre;
}

function putMonnaie(){
    var selectBox = document.getElementById("monnaie");
    var selected = selectBox.options[selectBox.selectedIndex].value;

    document.getElementById("monH").innerHTML = selected;
    document.getElementById("monT").innerHTML = selected;
    calulAll();

}

function validation(form){
    $(".fadeMe").css("visibility","visible");
    var url = $(form).attr("action");
    var formData = $(form).serializeArray();
    $.post(url, formData).done(function (data) {

        if(Number.isInteger(data.response)){
            if(form == '#newDevis'){
                window.location = "/view-devis/?id="+data.response;
            }
            else{
                window.location = "/view-facture/?id="+data.response;
            }
        }
        else{
            $(".fadeMe").css("visibility","hidden");
            messageUpdate(data.response,"error");
        }
    }).fail( function(xhr, textStatus, errorThrown) {
        document.getElementById("error").innerHTML = xhr.responseText;
    });
}

function validationUpdate(form,id){
    $(".fadeMe").css("visibility","visible");
    var url = $(form).attr("action");
    var formData = $(form).serializeArray();
    $.post(url, formData).done(function (data) {
        if(data.response == "OK"){
            if(form == '#newDevis'){
                window.location = "/view-devis/?id="+id;
            }
            else{
                window.location = "/view-facture/?id="+id;
            }
        }
        else{
            $(".fadeMe").css("visibility","hidden");
            messageUpdate(data.response,"error");
        }
    }).fail( function(xhr, textStatus, errorThrown) {
        document.getElementById("error").innerHTML = xhr.responseText;
    });
}

var rPercent = 0;

function remiseClient(){
    var remise = document.getElementById("remise").value;
    $("#articles .puht").each(function() {
        var t = parseFloat($(this).val()).toFixed(2);
        if(remise != 0 && remise != null && remise != ""){
            if(rPercent > 0){
                if(montants[$(this).attr("id")] > 0){
                    t = parseFloat(montants[$(this).attr("id")]).toFixed(2);
                }
            }
            t = t - (t*parseFloat(remise)/100);
            $(this).val(parseFloat(t).toFixed(2));
            var idT = $(this).attr("id");
            var id = idT.split("-");
            var qte = document.getElementById("qte-"+id[1]).value ;
            var totalHT = parseFloat(qte*t);
            document.getElementById("ptht-"+id[1]).value = parseFloat(totalHT).toFixed(2);
        }
        else{
            t = parseFloat(montants[$(this).attr("id")]).toFixed(2);
            $(this).val(parseFloat(t).toFixed(2));
            var idT = $(this).attr("id");
            var id = idT.split("-");
            var qte = document.getElementById("qte-"+id[1]).value ;
            var totalHT = parseFloat(qte*t);
            document.getElementById("ptht-"+id[1]).value = parseFloat(totalHT).toFixed(2);
        }
    });
    if(remise != 0 && remise != null && remise != "") {
        rPercent = remise;
    }
    calulAll();
}