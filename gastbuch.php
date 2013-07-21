<html>
<head>
<title>Neuer Eintrag in unser Gästebuch</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
</head>
<body bgcolor="#ffffff">
<h1 align="left"><font face ="arial">Neuer Eintrag in unser Gästebuch</font></h1>

<?php


$name=$_POST['name'];
$gastdatei=$_POST['gastdatei'];
$logdatei=$_POST['logdatei'];
$uselog=$_POST['uselog'];
$linkmail=$_POST['linkmail'];
$linkhomepage=$_POST['linkhomepage'];
$metastring=$_POST['metastring'];
$log=$_POST['log'];
$date=$_POST['date'];
$t=$_POST['t'];
$rh=$_POST['rh'];
$kommentar=$_POST['kommentar'];
$gast=$_POST['gast'];
$lines=$_POST['lines'];
$zeile=$_POST['zeile'];
$email=$_POST['email'];
$homepage=$_POST['homepage'];
$stadt=$_POST['stadt'];
$land=$_POST['land'];
$nr=$_POST['nr'];
$submit=$_POST['submit'];
$time=$_POST['time'];

// gastbuch.php for LINUX
// Version 0.1 (24. 10. 1999)
// Version 0.2 (12. 01. 2000) - Mit Homepage und Filterung von < und >
// Version 0.4 (5. 11. 2005)
// Optionen setzen
$gastdatei = "gastbuch.htm";
$logdatei  = "../../../kasumir.htm";
$uselog = 0;         // 1 = Ja; 0 = Nein
$linkmail = 0;       // 1 = Ja; 0 = Nein
$linkhomepage = 0;   // 1 = Ja; 0 = Nein

// Suchstring festlegen
$metastring = "!--begin-->";

if (!$_GET['action']=='rechnen') {
?>

<font face ="arial">
Sie können sich hier in unser Gästebuch eintragen. Bitte füllen Sie dieses Formular aus. 
Sie müssen nur <b>Ihren Namen</b> und einen <b>Kommentar</b> eintragen. Danke!</font>
<hr>
<form action="gastbuch.php?action=rechnen" method="post">

<table border="0"> 
<tr><td><font face ="arial">Ihr Name:</font></td>
<td><input type="text" name="name" size="30"></td></tr> 
<tr><td><font face ="arial">E-mail:</font></td>
<td><input type="text" name="email" size="40"></td></tr> 
<tr><td><font face ="arial">Homepage:</font></td>
<td><font face ="arial">http://</font><input type="text" name="homepage" size="40"></td></tr> 
<tr><td><font face ="arial">Stadt:</font></td>
<td><input type="text" name="stadt" size="15">
<font face ="arial">Land: <input type="text" value="Österreich" name="land" size="15"></font></td></tr> 
<tr><td><font face ="arial">Kommentar:</font></td>
<td><textarea name="kommentar" COLS="60" ROWS="4"></textarea></td></tr> 
</table>
<table border="0"><tr>
<td><font face ="arial"><input type="submit" name="submit" value="Eintragung absenden"></font></td>
<td><font face ="arial"><input type="reset" value="Alles löschen"></font></td>
</tr></table>

</form>

<?php
}
 else {
 // Fehlermeldung bei leeren Feldern

// **************************************************************
function print_zurueck () {
  print "<FORM>\n";
  print "<INPUT TYPE=\"BUTTON\" VALUE=\"Zurück zur Eingabe\" onClick=\"history.back()\">\n";
  print "</FORM>\n";
 }

// **************************************************************
function print_bitte () {
  print "Es wurde nichts in das Gästebuch eingetragen. Bitte geben Sie ";
 }

$t = getdate();
$date  =  $t["mday"].". ".$t["mon"].". ".$t["year"];
$time = $t["hours"].".".$t["minutes"].".".$t["seconds"] ;
// **************************************************************
function no_comments() {
  global $uselog,$logdatei,$date;
  print "<b>Sie haben keinen Kommentar eingegeben!</b>\n";
  print_bitte ();
  print "Ihren Kommentar ein.<p>\n";
  print_zurueck ();

// Fehler eintragen
  if ($uselog) {
    $log = fopen ($logdatei,"a");
    $rh = getenv (REMOTE_ADDR);
    fwrite ($log, "$rh ($date) <b>Fehler</b> - Kein Kommentar<br>\n");
    fclose ($log);
    }
}

// **************************************************************
function no_name () {
  global $uselog,$logdatei,$date;
  print "<b>Sie haben Ihren Namen nicht eingegeben!</b>\n";
  print_bitte();
  print "Ihren Namen ein.<p>\n";
  print_zurueck ();

# Fehler eintragen
if ($uselog) {
   $log = fopen ($logdatei,"a");
   $rh = getenv (REMOTE_ADDR);
   fwrite ($log, "$rh - ($date) <b>Fehler</b> - Kein Name<br>\n");
   fclose ($log);
   }
}

// **************************************************************
function kommentarfehler () {
  global $uselog,$logdatei,$date;
  print "<b>Die Zeichen &lt; und &gt; dürfen nicht im Kommentar eingegeben werden!</b>\n";
  print_bitte();
  print "den Kommentar nochmals ein.<p>\n";
  print_zurueck ();

# Fehler eintragen
if ($uselog) {
   $log = fopen ($logdatei,"a");
   $rh = getenv ("REMOTE_ADDR");
   fwrite ($log, "$rh - ($date) <b>Fehler</b> - Kommentar mit &lt; oder &gt;<br>\n");
   fclose ($log);
   }
}
// **************************************************************
function kommentar_ok ($kommentar) {
 $kommentar = " " . $kommentar;
 if (strpos ($kommentar, "<") > 0) {return 0;}
 if (strpos ($kommentar, ">") > 0) {return 0;}
 return 1;
} // kommentar_ok ($kommentar)

// **************************************************************
function eintragen () {
 global $uselog,$logdatei,$gastdatei,$metastring,$date,$time,$linkmail,$linkhomepage;
 global $name,$email,$homepage,$stadt,$land,$kommentar;
 // Gaestebuch öffnen
 $gast = fopen ($gastdatei,"r");
 $lines = array();
 while ($zeile = fgets($gast,1024)) { $lines[count($lines)] = $zeile; }
 fclose($gast);

 // Zeilenumbruch durch HTML-Tag ersetzen
 $kommentar = ereg_replace("\n", "<br>", $kommentar);
 $kommentar = ereg_replace("\r", "\n", $kommentar);

 // und Kommentar eintragen
 $gast = fopen ($gastdatei,"w");

 for ($nr = 0; $nr < count($lines); $nr++) {
  $zeile = $lines[$nr];
  if (strpos ($zeile,$metastring) > 0) {
    fwrite ($gast, $zeile);
    fwrite ($gast, "<p>$kommentar</p>\n");
    fwrite ($gast, "Von: <b>$name</b>");
    if ($email){
      if ($linkmail) { fwrite ($gast, " (<a href=\"mailto:$email\">$email</a>)"); }
         else { fwrite ($gast, " ($email)"); }
      }
    if ($homepage){
      if ($linkhomepage) { fwrite ($gast, ", Homepage <a href=\"http://$homepage\">http://$homepage</a>"); }
         else { fwrite ($gast, " Homepage: http://$homepage"); }
      }
    fwrite ($gast, "<br>\n");
    if ($stadt){ fwrite ($gast, "aus $stadt, "); }
    if ($land) { fwrite ($gast, "$land"); }
    fwrite ($gast, " - $date<hr>\n\n");
    }
    else { fwrite ($gast, $zeile); }
   }
 fclose ($gast);

 // Log-Datei schreiben
 if ($uselog) {
   $log = fopen ($logdatei, "a");
   $rh = getenv("REMOTE_ADDR");
   fwrite ($log, "$rh ($date) ($time) [$email] ($name)<br>\n");
   fclose ($log);
   }

 # Zurueck zur Eingabeseite
 print "<b>Danke für Ihre Eintragung in unser Gästebuch</b><br>\n";

 # Antwort drucken
 print "Folgender Eintrag wird nach Freigabe in unser Gästebuch aufgenommen:<hr>\n";
 print "<p>$kommentar</p>\n";
 print "<b>$name</b>";
  if ($email){
     if ($linkmail) { print " (<a href=\"mailto:$email\">$email</a>)"; }
     else { print " ($email)"; }
  }
  if ($homepage){
     if ($linkhomepage) { print ", Homepage <a href=\"http://$homepage\">http://$homepage</a>"; }
     else { print " Homepage: http://$homepage"; }
  }
 print "<br>\n";
   if ($stadt){ print "$stadt,"; }
   if ($land){ print " $land"; }
 print " - $date<p>\n";
} // end eintragen ()

// Zu Fehlermeldungen verzweigen, wenn Eingabe fehlt
if (!$name) {no_name();}
   elseif (!$kommentar) {no_comments();}
   else {if (kommentar_ok($kommentar)) {eintragen ();}
            else {kommentarfehler();}
        }
} // end else
?>
<a href="gastbuch.htm"><font face ="arial">Zurück zum Gästebuch</font></a>
</body>
</html>

