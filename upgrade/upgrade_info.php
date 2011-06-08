<?php header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META http-equiv=Content-Type content="text/html; charset=UTF-8">
<title>Οδηγίες Αναβάθμισης Πλατφόρμας Open eClass 2.4</title>
<link href="../template/classic/theme.css" rel="stylesheet" type="text/css" />
<style type="text/css">
p {
 text-align: justify;
}
</style>
  </head>
  <body>
  
  <div id="container" style="padding: 30px;">
  <div id="header"> 

<a href="http://www.openeclass.org/" title="Open eClass" class="logo"></a></div>
    
<p class="title1">Οδηγίες Αναβάθμισης Πλατφόρμας Open eClass 2.4</p>

<p>Η νέα έκδοση <b>(2.4)</b> της πλατφόρμας διατηρεί τη συμβατότητα με τις προηγούμενες εκδόσεις.
Για το λόγο αυτό μπορείτε εύκολα και γρήγορα να αναβαθμίσετε μια ήδη εγκατεστημένη πλατφόρμα 
από τις προηγούμενες εκδόσεις στην τρέχουσα, απλά ακολουθώντας τις οδηγίες αναβάθμισης
που παραθέτουμε στη συνέχεια.</p>
<div class="alert1">
<ul>
<li>Αρχικά βεβαιωθείτε ότι κατά τη διάρκεια της αναβάθμισης δεν γίνονται μαθήματα,
ούτε υπάρχει πρόσβαση στις βάσεις δεδομένων της ήδη εγκατεστημένης πλατφόρμας Open eClass. 
</li>
<li>Ελέγξτε την έκδοση της πλατφόρμας ακολουθώντας το σύνδεσμο «Ταυτότητα Πλατφόρμας» στην αρχική σελίδα.
Για να είναι δυνατή η παρακάτω διαδικασία αναβάθμισης, η ήδη εγκατεστημένη πλατφόρμα θα πρέπει να είναι έκδοσης <b>&gt;=2.0</b>
Αν αναβαθμίζετε από παλιότερη έκδοση ακολουθήστε τις οδηγίες που αναγράφονται στο τέλος του παρόντος.
</li></ul></div>
<p>Επίσης, πριν ξεκινήσετε τη διαδικασία αναβάθμισης, προτείνεται η λήψη αντίγραφου ασφαλείας των περιεχομένων των μαθημάτων
και των βάσεων δεδομένων.</p>
<p class="sub_title1">Επιγραμματικά για την αναβάθμιση της πλατφόρμας στη νέα έκδοση τα βήματα που πρέπει να ακολουθήσετε είναι τα εξής:</p>
<ul>
 <b>Βήμα 1</b>
 <li><a href="#unix">Αναβάθμιση σε υπολογιστές με λειτουργικό σύστημα Unix
    / Linux (Solaris, Redhat, CentOS, Debian, Suse, Mandrake κ.λπ.)</a></li>
 <li><a href="#win">Αναβάθμιση σε υπολογιστές με λειτουργικό σύστημα Ms Windows
    (Windows2000, WindowsXP, Windows2003, Windows Vista, Windows 7 κ.λπ.)</a></li>
	</ul>
  <ul>
 <b>Βήμα 2</b>
 <li><a href="#dbase">Αναβάθμιση της Βάσης Δεδομένων</a></li>
	</ul>
        <ul>
 <b>Βήμα 3</b>
 <li><a href="#after">Έλεγχος επιτυχούς αναβάθμισης</a></li>
	</ul>
        <ul>
  <b>Βήμα 4</b>
  <li><a href="#other">Προαιρετικές επιπλέον ρυθμίσεις</a></li>
</ul>

<p><a href="#oldest">Αναβάθμιση από παλιότερες εκδόσεις (&lt;= 2.0)</a></p>

<br>
<p class="title1" id="unix">Βήμα 1: Για υπολογιστές με λειτουργικό σύστημα Unix / Linux</p>
<p class='sub_title1'>Διαδικασία αναβάθμισης</p>
<p>Όλες οι ενέργειες προϋποθέτουν ότι έχετε δικαιώματα διαχειριστή (root) στον εξυπηρετητή.</p>
<p>Το ακόλουθο παράδειγμα θεωρεί ότι η πλατφόρμα eClass είναι ήδη εγκατεστημένη
  στον κατάλογο <code>/var/www/html</code>.</p>
<p>Λόγω αρκετών αλλαγών στη καινούρια έκδοση (2.4) του Open eClass θα πρέπει να διαγράψετε
  την παλιά έκδοση και να εγκαταστήσετε την καινούρια. 
  Για να μην χαθούν οι παλιές σας ρυθμίσεις, θα πρέπει να κάνετε τα παρακάτω:</p>
<p>θεωρούμε ότι έχετε κατεβάσει το <b>openeclass-2.4.tar.gz</b> στο κατάλογο <code>/tmp
  </code></p>
<ul>
  <li>Μεταβείτε στον κατάλογο που έχετε εγκατεστημένο το eClass. π.χ.
    <pre>cd /var/www/html</pre>
  <li> Μετακινήστε το αρχείο των ρυθμίσεων (<em>eclass/config/config.php</em>)
    σε ένα άλλο προσωρινό κατάλογο. Μια καλή λύση είναι ο κατάλογος <em>/tmp</em>
    π.χ.
    <pre>mv /var/www/html/eclass/config/config.php /tmp</pre>
  </li>
  <li>Διαγράψτε τους καταλόγους του μαθήματος εκτός των courses και config
     Π.χ.
    <pre>cd /var/www/html/eclass/
rm -rf images/ include/ info/ install/ manuals/ template/ modules/ </pre>
  </li>
  <li>Αποσυμπιέστε το <b>openeclass-2.4.tar.gz</b> σε ένα προσωρινό φάκελο (/tmp) π.χ.
    <pre>tar xzvf /tmp/openeclass-2.4.tar.gz</pre>

	Κατόπιν αντιγράψτε (copy) από τον προσωρινό φάκελο /tmp/openeclass-2.4 όλα τα περιεχόμενα του 
	(δηλαδή αρχεία και φακέλους) στον κατάλογο της εγκατάστασης πχ.
	<pre>cp -a /tmp/openeclass-2.4/*  /var/www/html/eclass/</pre>
	
	Με τον τρόπο αυτό, αντικαθίσταται ο φάκελος eclass, από αυτόν της νέας διανομής Open eClass 2.4.
  </li>
  <li>Μετακινήστε το αρχείο <em>config.php</em> στον κατάλογο <em>config</em>.
    π.χ.
    <pre>mv /tmp/config.php /var/www/html/eclass/config/</pre>
  <li>Διορθώστε (αν χρειάζεται) τα permissions των αρχείων και των υποκαταλόγων
    δίνοντας για παράδειγμα τις παρακάτω εντολές: (υποθέτοντας ότι ο user με τον
    οποίο τρέχει ο apache είναι ο www-data)
    <pre>cd /opt/eclass
chown -R www-data *
find ./ -type f -exec chmod 664 {} \;
find ./ -type d -exec chmod 775 {} \;
</pre>
  </li>
</ul>
<p>Μόλις ολοκληρωθούν τα παραπάνω, θα έχετε εγκαταστήσει με επιτυχία τα αρχεία
  της νέας έκδοσης (Open eClass 2.4). Στη συνέχεια μεταβείτε στο  <a href="#dbase">βήμα 2</a>
  για να αναβαθμίσετε τις βάσεις δεδομένων της πλατφόρμας.</p><br />

<p class="title1" id="win">Βήμα 1: Αναβάθμιση σε Υπολογιστές με Λειτουργικό Σύστημα Ms Windows</p>
<p class='sub_title1'>Διαδικασία αναβάθμισης</p>
<p>Το ακόλουθο παράδειγμα προϋποθέτει ότι το eClass είναι ήδη εγκατεστημένο στον
  κατάλογο <tt>C:\Program Files\Apache\htdocs\</tt> και ότι έχετε κατεβάσει
  το <b>openeclass-2.4.zip</b>.</p>
<p>Λόγω αρκετών αλλαγών στη καινούρια έκδοση (2.4) του Open eClass θα πρέπει να διαγράψετε
  την παλιά έκδοση και να εγκαταστήσετε την καινούρια. Για να μην χαθούν όμως οι παλιές σας ρυθμίσεις και
  τα μαθήματα που έχουν δημιουργηθεί, θα πρέπει να κάνετε τα παρακάτω.</p>
<ul>
  <li>Μεταβείτε στον κατάλογο που έχετε εγκατεστημένο το Open eClass. π.χ. <tt>C:\Program
    Files\Apache\htdocs</tt></li>
  <li>Μετακινήστε το αρχείο των ρυθμίσεων <tt>C:\Program Files\Apache\htdocs\eclass\config\config.php</tt>
    σε ένα άλλο προσωρινό φάκελο στην επιφάνεια εργασίας. π.χ. από το <tt>C:\Program
    Files\Apache\htdocs\eclass\config\config.php</tt> στο κατάλογο <tt>C:\Documents
    and Settings\Administrator\Desktop\</tt></li>
  <li>Μπείτε στο κατάλογο που είναι εγκατεστημένο το eclass δηλαδή <tt>C:\Program
    Files\Apache\htdocs\eclass\</tt> και διαγράψτε τους καταλόγους <em>images, include, info, install, manuals, template, modules</em>
    μαζί με τους υποκαταλόγους τους.</li>
  <li>Αποσυμπιέστε το openeclass-2.4.zip σε ένα προσωρινό φάκελο στην επιφάνεια εργασίας.
    π.χ. <tt>C:\Documents and Settings\Administrator\Desktop\openeclass-2.4</tt>.
    Κατόπιν μετονομάστε τον προσωρινό φάκελο openeclass-2.4 σε eclass και αντιγράψτε τον (copy) μαζί 
	με όλα τα περιεχόμενα του (δηλαδή αρχεία και φακέλους). Στη συνέχεια ανοίξτε το φάκελο 
	που περιέχει την εγκατάσταση του Open eClass, π.χ.  <tt>C:\Program
    Files\Apache\htdocs\</tt> και κάντε επικόλληση (paste). Με τον τρόπο αυτό,
    αντικαθίσταται ο φάκελος eclass, από αυτόν της νέας διανομής.
  </li>
  <li>Τέλος διαγράψτε το φάκελο στην επιφάνεια εργασίας όπου προσωρινά αποσυμπιέσαμε
    τη νέα διανομή.</li>
	</ul>
	<p>Μόλις ολοκληρωθούν τα παραπάνω θα έχετε εγκαταστήσει με επιτυχία τα αρχεία
    της νέας έκδοσης του Open eClass. Στη συνέχεια μεταβείτε στο  <a href="#dbase">βήμα 2</a>
    για να αναβαθμίσετε τις βάσεις δεδομένων του.</p><br />

<p class="title1" id="dbase">Βήμα 2: Αναβάθμιση της Βάσης Δεδομένων</p>
<div class="info">
  <p><b>Μόνο για συστήματα Unix/Linux: </b>Η διαδικασία αναβάθμισης περιλαμβάνει
    και κάποιες αλλαγές στο αρχείο ρυθμίσεων<em> config.php</em>. Επομένως μπορεί
    να χρειαστεί να αλλάξετε προσωρινά τα δικαιώματα πρόσβασης στο <em>config.php</em>.</p>
  </div>
<p>Πληκτρολογήστε στον browser σας το ακόλουθο URL:</p>
<tt>http://(url του eclass)/upgrade/</tt>
<p>Θα σας ζητηθεί το όνομα χρήστη (username) και συνθηματικό (password) του διαχειριστή
  της πλατφόρμας. Αφού δώσετε τα στοιχεία σας θα σας ζητηθεί να αλλάξετε / διορθώσετε
  τα στοιχεία επικοινωνίας. Κατόπιν θα αρχίσει η αναβάθμιση των βάσεων δεδομένων.
  Στην οθόνη σας θα δείτε διάφορα μηνύματα σχετικά με την πρόοδο της εργασίας.
  Φυσιολογικά δεν θα πρέπει να δείτε μηνύματα λάθους.
 Σημειώστε, ότι ανάλογα με τον αριθμό και το περιεχόμενο των μαθημάτων, είναι πιθανόν η διαδικασία να διαρκέσει αρκετά.
</p>
<p>Στην αντίθετη περίπτωση (αν δηλαδή εμφανιστούν μηνύματα λάθους) τότε πιθανόν
  να μην λειτουργήσει εντελώς σωστά κάποιο μάθημα. Τέτοια μηνύματα λάθους μπορεί
  να εμφανιστούν, αν έχετε τροποποιήσει τη δομή κάποιου πίνακα από τις βάσεις
  του eClass. Σημειώστε (αν είναι δυνατόν) το ακριβές μήνυμα λάθους που σας εμφανίστηκε.</p>
<p>Αν μετά την αναβάθμιση αντιμετωπίσετε προβλήματα με κάποιο μάθημα τότε επικοινωνήστε
  μαζί μας (<a href="mailto:info@openeclass.org">info@openeclass.org</a>).</p><br />

<p class="title1" id="after">Βήμα 3: Έλεγχος επιτυχούς αναβάθμισης</p>
<p>Για να βεβαιωθείτε ότι η πλατφόρμα έχει αναβαθμιστεί, πηγαίνετε στο διαχειριστικό
  εργαλείο. Ανάμεσα στα άλλα Θα πρέπει να αναγράφεται η έκδοση <em>2.4</em>.
  Εναλλακτικά, από την αρχική σελίδα της πλατφόρμας, επιλέξτε το σύνδεσμο "Ταυτότητα Πλατφόρμας"
  όπου θα αναγράφεται η έκδοση <b>2.4</b>.</p>

<p>Είστε έτοιμοι! Η διαδικασία αναβάθμισης έχει ολοκληρωθεί με επιτυχία! Για να δείτε τα καινούρια χαρακτηριστικά
της νέας έκδοσης ανατρέξτε στο αρχείο κειμένου <a href="CHANGES_el.txt">CHANGES.txt</a>.
Για επιπλέον προαιρετικές ρυθμίσεις διαβάστε παρακάτω.</p><br />

<p class="title1" id="other">Βήμα 4: Προαιρετικές επιπλέον ρυθμίσεις</p>
<ul><li>Στο αρχείο <em>config.php</em> θα έχει οριστεί η μεταβλητή <em>close_user_registration</em>
  η οποία εξ'ορισμού έχει τιμή <em>FALSE</em>. Αλλάζοντας την σε τιμή <em>TRUE</em>
  η εγγραφή χρηστών με δικαιώματα "φοιτητή" δεν θα είναι πλέον ελεύθερη. Οι χρήστες
  για να αποκτήσουν λογαριασμό στην πλατφόρμα θα ακολουθούν πλέον διαδικασία παρόμοια
  με τη δημιουργία λογαριασμού "καθηγητή" δηλαδή θα συμπληρώνουν μια φόρμα-αίτηση
  δημουργίας λογαριασμού φοιτητή. Η αίτηση εξετάζεται από τον διαχειριστή ο οποίος
  εγκρίνει την αίτηση, οπότε δημιουργεί τον λογαριασμό, ή την απορρίπτει. Αν δεν
  επιθυμείτε να αλλάξει ο τρόπος εγγραφής φοιτητών δεν χρειάζεται να την ορίσετε.
  </li>
<li>
<p>Αν θέλετε να αλλάξετε οποιοδήποτε μήνυμα της πλατφόρμας συνίσταται να το κάνετε ως εξής:
Δημιουργήστε ένα αρχείο τύπου .php με όνομα <em>greek.inc.php</em> (ή <em>english.inc.php</em> αν πρόκειται για αγγλικά μηνύματα) και τοποθετήστε το στον κατάλογο <em>(path του eclass)/config/</em>. Αναζητήστε το όνομα της μεταβλητής που περιέχει το μήνυμα που θέλετε να αλλάξετε και απλά αναθέστε της το καινούριο μήνυμα. Π.χ.
Αν θέλουμε να αλλάξουμε το μήνυμα <pre>$langAboutText = "Η έκδοση της πλατφόρμας είναι";</pre> απλά δημιουργούμε το <em>greek.inc.php</em> στον κατάλογo (path του eclass)/config/ ως εξής:
<pre>
&lt;?php
$langAboutText = "Τρέχουσα έκδοση της πλατφόρμας";
</pre>
Με τον παραπάνω τρόπο εξασφαλίζεται η διατήρηση των τροποποιημένων μηνυμάτων από μελλοντικές αναβαθμίσεις της πλατφόρμας.
<p>
Μπορείτε να αλλάξετε τα ονόματα των βασικών ρόλων των χρηστών της πλατφόρμας προσθέτοντας στο παραπάνω αρχείο νέους ορισμούς για τις μεταβλητές που βρίσκονται στο αρχείο  <em>(path του eClass)/modules/lang/greek/common.inc.php</em>
</p>
<p>Επίσης σημειώστε ότι μπορείτε να προσθέσετε κείμενο (π.χ. ενημερωτικού περιεχομένου) στα αριστερά και δεξιά της αρχικής σελίδας της πλατφόρμας. Για το σκοπό αυτό, αναθέστε την τιμή - μήνυμα στις μεταβλητές <em>$langExtrasLeft</em> και <em>$langExtrasRight</em> αντίστοιχα.
</p>
</li>
<li>Η πλατφόρμα υποστηρίζει την συγγραφή μαθηματικών συμβόλων στα υποσύστηματα "Ασκήσεις", "Περιοχές συζητήσεων" και "Ανακοινώσεις". Συγκεκριμένα στο υποσύστημα "Ασκήσεις" μπορείτε να βάλετε μαθηματικά σύμβολα στα πεδία "Περιγραφή Άσκησης" όταν δημιουργείτε μια καινούρια άσκηση (ή όταν την διορθώνετε), στο πεδίο "Προαιρετικό Σχόλιο" όταν δημιουργείτε μια καινούρια ερώτηση σε μια άσκηση (ή όταν την διορθώνετε). Στο υποσύστημα "Περιοχές συζητήσεων" όταν συντάσσετε ένα καινούριο μήνυμα ή όταν απαντάτε σε αυτό και στο υποσύστημα "Ανακοινώσεις" όταν δημιουργείτε μια ανακοίνωση. Τα μαθηματικά σύμβολα πρέπει απαραίτητα να περικλείονται με τα tags <em>[m]</em> και <em>[/m]</em>.
Π.χ. πληκτρολογώντας
<pre>
[m]sqrt{x-1}[/m]
</pre>
θα σχηματιστεί η τετραγωνική ρίζα του x-1. Για την σύνταξη των υπόλοιπων μαθηματικών συμβόλων ανατρέξτε <a href="../manuals/PhpMathPublisherHelp.pdf"><em>στις σχετικές οδηγίες</em></a>. Σημειώστε ότι σε παλαιότερες εκδόσεις τα tags για τα μαθηματικά σύμβολα ήταν <em>&lt;m></em> και <em>&lt;/m></em>, που υποστηρίζονται ακόμα, αλλά συνιστάται η χρήση των tags με αγκύλες.
</li>
<li>
<p> Αν θέλετε να χρησιμοποιήσετε την πλατφόρμα με Web server που έχει ενεργοποιημένη την υποστήριξη SSL 
(π.χ. https://eclass.gunet.gr) μπορείτε να το κάνετε δηλώνοντας στο <em>config.php</em> την μεταβλητή
<em>urlSecure</em>. π.χ. <code>$urlSecure = "https://eclass.gunet.gr"</code>. Περισσότερες και αναλυτικότερες 
οδηγίες για τις ενέργειες αυτές, μπορείτε να βρείτε στο εγχειρίδιο του Διαχειριστή (βρίσκεται μέσα στο διαχειριστικό εργαλείο).
</p>
</li>
</ul>
<p class='sub_title1'>Ρυθμίσεις του πίνακα config</p>
<p>Η νέα έκδοση της πλατφόρμας δημιουργεί τον πίνακα <em>config</em>. Σε αυτό τον πίνακα,
κάθε γραμμή του αντιστοιχεί σε μία (προαιρετική) ρύθμιση της πλατφόρμας.
Μπορούν να τροποποιηθούν, από το διαχειριστικό εργαλείο της πλατφόρμας. Αυτές είναι:</p>
<ul>
 <li><em>email_required</em>: Το email των χρηστών, κατά την εγγραφή τους, θα είναι υποχρεωτικό.</li>
 <li><em>am_required</em>: Ο αριθμός μητρώου των χρηστών, κατά την εγγραφή τους, θα είναι υποχρεωτικός.</li>
 <li><em>dropbox_allow_student_to_student</em>: Θα επιτρέπεται η ανταλλαγή αρχείων μεταξύ χρηστών στο υποσύστημα 'Ανταλλαγή αρχείων'.</li>
 <li><em>dont_display_login_form</em>: Δεν θα εμφανίζεται στην αρχική σελίδα της πλατφόρμας η οθόνη σύνδεσης και θα εμφανίζεται ένας σύνδεσμος προς αυτήν.</li>
 <li><em>block_username_change</em>: Δεν θα επιτρέπεται να αλλάζουν οι χρήστες το 'όνομα χρήστη'.</li>
 <li><em>display_captcha</em>: Να εμφανίζεται κωδικός ασφάλειας κατά την εγγραφή νέων χρηστών.</li>
 <li><em>insert_xml_metadata</em>: Να επιτρέπεται στους εκπαιδευτές να ανεβάσουν μεταδεδομένα σε αρχεία του υποσύστηματος 'Εγγραφα'.</li>
</ul>
Εξ' ορισμού, καμμία ρύθμιση από τις παραπάνω, δεν είναι ενεργοποιημένη.
<ul>

 <li><em>doc_quota</em>: Καθορίζεται το εξ' ορισμού όριο αποθηκευτικού χώρου μαθήματος για το υποσύστημα «Έγγραφα». 
 <li><em>video_quota</em>: Καθορίζεται το εξ' ορισμού όριο αποθηκευτικού χώρου μαθήματος για το υποσύστημα «Βίντεο».
 <li><em>dropbox_quota</em>: Καθορίζεται το εξ' ορισμού όριο αποθηκευτικού χώρου μαθήματος για το υποσύστημα «Ανταλλαγή αρχείων».
 <li><em>group_quota</em>: Καθορίζεται το εξ' ορισμού όριο αποθηκευτικού χώρου μαθήματος για το  υποσύστημα «Ομάδες Χρηστών». 
</ul>
<br />

<p class="title1" id="oldest">Αναβάθμιση από παλιότερες εκδόσεις (&lt;= 2.0)</p>
<ul>
<li>Αν η έκδοση της πλατφόρμας είναι &lt;= 1.7 τότε θα πρέπει να αναβαθμίσετε πρώτα σε έκδοση 1.7
ακολουθώντας τις οδηγίες που παρατίθονται <a href="http://www.openeclass.org/downloads/files/docs/1.7/Upgrade.pdf" target=_blank>εδώ</a>
και κατόπιν να αναβαθμίσετε στην έκδοση 2.0.</li>
<li>Αν η έκδοση της πλατφόρμας είναι η 1.7 τότε θα πρέπει να αναβαθμίσετε πρώτα σε έκδοση 2.0 ακολουθώντας τις οδηγίες που παρατίθονται
<a href="http://www.openeclass.org/downloads/files/docs/2.0/Upgrade.pdf" target=_blank>εδώ</a>.</li>
</ul>
    </div>	
  </body>
</html>
