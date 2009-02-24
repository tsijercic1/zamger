<?

// NASTAVNIK/PREDMET - pocetna stranica za administraciju predmeta - izbor studentskih modula

// v3.9.1.0 (2008/02/18) + Preimenovan bivsi admin_predmet
// v3.9.1.1 (2008/04/09) + Usavrsen login
// v3.9.1.2 (2008/12/23) + Akcija "set_smodul" prebacena na POST radi zastite od CSRF (bug 58)
// v4.0.0.0 (2009/02/19) + Release
// v4.0.0.1 (2009/02/24) + Dodan prikaz nastavnika angazovanih na predmetu



function nastavnik_predmet() {

global $userid,$user_siteadmin;



$predmet=intval($_REQUEST['predmet']);
if ($predmet==0) { 
	zamgerlog("ilegalan predmet $predmet",3); //nivo 3: greska
	biguglyerror("Nije izabran predmet."); 
	return; 
}

$q1 = myquery("select p.naziv from predmet as p, ponudakursa as pk where pk.id=$predmet and pk.predmet=p.id");
$predmet_naziv = mysql_result($q1,0,0);

//$tab=$_REQUEST['tab'];
//if ($tab=="") $tab="Opcije";

//logthis("Admin Predmet $predmet - tab $tab");



// Da li korisnik ima pravo pristupa

if (!$user_siteadmin) { // 3 = site admin
	$q10 = myquery("select np.admin from nastavnik_predmet as np where np.nastavnik=$userid and np.predmet=$predmet");
	if (mysql_num_rows($q10)<1 || mysql_result($q10,0,0)<1) {
		zamgerlog("nastavnik/predmet privilegije (predmet p$predmet)",3);
		biguglyerror("Nemate pravo pristupa");
		return;
	} 
}



?>

<p>&nbsp;</p>

<p><h3><?=$predmet_naziv?> - Opcije predmeta</h3></p>

<?


// Prikaz angažovanih nastavnika i saradnika

?>

<p>Pristup predmetu imaju sljedeći nastavnici i saradnici (slovo A označava da saradnik ima administratorske privilegije):</p>

<ul>
<?

$q100 = myquery("select o.ime, o.prezime, np.admin from osoba as o, nastavnik_predmet as np where np.nastavnik=o.id and np.predmet=$predmet");
while ($r100 = mysql_fetch_row($q100)) {
	if ($r100[2]==1) $dodaj=" (A)"; else $dodaj="";
	print "<li>$r100[0] $r100[1]$dodaj</li>\n";
}

?>
</ul>

<?


// Opcije predmeta

?>

<SCRIPT language="JavaScript">
function upozorenje(smodul,aktivan) {
	document.smodulakcija.smodul.value=smodul;
	document.smodulakcija.aktivan.value=aktivan;
	document.smodulakcija.submit();
}
</SCRIPT>
<?=genform("POST", "smodulakcija")?>
<input type="hidden" name="akcija" value="set_smodul">
<input type="hidden" name="smodul" value="">
<input type="hidden" name="aktivan" value="">
</form>

<p>Izaberite opcije koje želite da učinite dostupnim studentima:<br/>
<?


// Click na checkbox za dodavanje modula
// Prebaciti na POST?

if ($_POST['akcija'] == "set_smodul" && check_csrf_token()) {
	$smodul = intval($_POST['smodul']);
	if ($_POST['aktivan']==0) $aktivan=1; else $aktivan=0;
	$q15 = myquery("update studentski_moduli set aktivan=$aktivan where id=$smodul");
	if ($aktivan==1)
		zamgerlog("aktiviran studentski modul $smodul (predmet p$predmet)",2); // nivo 2: edit
	else
		zamgerlog("deaktiviran studentski modul $smodul (predmet p$predmet)",2); // nivo 2: edit
}


// Studentski moduli koji su aktivirani za ovaj predmet

$q20 = myquery("select id,gui_naziv,aktivan from studentski_moduli where predmet=$predmet order by id");
if (mysql_num_rows($q20)<1)
	print "<p>Nijedan modul nije ponuđen.</p>\n";
while ($r20 = mysql_fetch_row($q20)) {
	$smodul = $r20[0];
	$naziv = $r20[1];
	$aktivan=$r20[2];
	if ($aktivan==0) $checked=""; else $checked="CHECKED";
	?>
	<input type="checkbox" onchange="javascript:onclick=upozorenje('<?=$smodul?>','<?=$aktivan?>')" <?=$checked?>> <?=$naziv?><br/>
	<?
}



}

?>