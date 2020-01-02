YCom Board für REDAXO 5.8
=========================


Beschreibung
------------
Das Board ist ein einfaches AddOn, um ein einfaches Messageboard für die YCom zu realisieren. Das AddOn wurde von einer 4er Version migriert.


Installation
------------

* Die Dateien in das Verzeichnis redaxo/src/addons/ycom_board kopieren
* Über die AddOn Verwaltung das AddOn installieren. Die notwendigen Datenbanktabellen werden dabei angelegt
* Bei Bedarf das Modul über den Menüpunkt im Backend Community/Einstellungen dann im Reiter YCom Board installieren
* Auf den Seiten, auf denen das Board verwendet werden soll das Modul platzieren.


Konfiguration
-------------

Das Modul des Boards erkennt, ob ein YCom Benutzer eingeloggt ist oder nicht. Wenn kein Benutzer eingeloggt ist, wird auch kein Eingabeformular angezeigt. Lediglich eingeloggte Benutzer können Meldungen schreiben.
Man kann sich als eingeloggter YCom Benutzer Benachrichtigungen schicken lassen, wenn neue Meldungen für ein Thema eingetragen werden.
Die Administration der Meldungen erfolgt über YForm Tabellen im REDAXO Backend.
Es können innerhalb einer Seite neue Themen im Board erstellt werden und hierauf Beiträge folgen. Eine weitere Verschachtelung der Beiträge ist nicht möglich.

Es muss noch ein E-Mail Template erstellt werden, das für die Benachrichtigung bei neuen Beiträgen verwendet wird.

---------
```
Guten Tag REX_YFORM_DATA[field="username"],

Sie haben die automatische Benachrichtigung für neue Einträge im Messageboard bei Vielfalt Schule eingerichtet.

Zum Thema "REX_YFORM_DATA[field='thread_title']" gibt es einen neuen Eintrag.

<?= trim(rex::getServer(),'/') ?>REX_YFORM_DATA[field="post_url"]

Sie können die automatische Benachrichtigung abstellen, indem Sie sich im Board einloggen und beim entsprechenden Thread auf "Benachrichtigung abstellen" klicken.
```
---------



Funktionen
----------

Das Board kann bei neuen Themen E-Mail Benachrichtigungen an alle Teilnehmer schicken. In den Einstellungen des Boards kann man eine oder mehrere Benutzergruppen festlegen, die keine Benachrichtigungsmail bekommen. So kann man beispielsweise die Benutzergruppe _no_messages_ einrichten. Wenn ein Benutzer Mitglied dieser Gruppe ist und dies in den Einstellungen so definiert ist, erhält er keine Benachrichtigungen bei neuen Themen. Wenn der Status eines Benutzers kleiner als 1 ist, wird ebenfalls keine Benachrichtigung verschickt.

Für die Benachrichtigungsmail muss ein E-Mail Template angelegt werden.

Im E-Mail Template sind folgende Felder verfügbar:

firstname - Vorname des Empfängers für personalisierte Mails
name - Nachname des Empfängers für personalisierte Mails
email - E-Mail des Empfängers
url - Adresse (ohne Server) der Seite auf der das neue Thema gepostet wurde
title - Titel des neuen Themas
message - Text des neuen Themas

Mustertemplate:

---
```
Hallo REX_YFORM_DATA[field="firstname"] REX_YFORM_DATA[field="name"]

Soeben wurde ein neuer Beitrag im Forum gepostet.

<?= trim(rex::getServer(),'/') ?>REX_YFORM_DATA[field="url"]

Titel: REX_YFORM_DATA[field="title"]

Text:
REX_YFORM_DATA[field="message"]


Sie erhalten diese Mail, weil Sie Mitglied im Forum sind. Wenn Sie sich abmelden, bekommen Sie keine Mails mehr.
```
---

In der YCom muss eine Gruppe erstellt werden, die als Board Admin Gruppe im Modul für jedes Board festgelegt werden kann. (Momentan noch ohne Funktion).


Multiuploadfeld
---------------

Das Board ist mit einem Multiuploadfeld ausgestattet. Damit können zu Beiträgen Dateien hochgeladen werden. Die hochgeladenen Dateien landen im Verzeichnis /media/ycom_board. Die Dateinamen werden gemäß REDAXO-Konvention normalisiert. Zusätzlich wird dem Dateinamen ein Timestamp und ein Unterstrich vorangestellt. Damit können auch Dateien gleichen Namens hochgeladen werden. Die Dateinamen werden in der Tabelle rex_ycom_board_post im Feld attachment als json abgelegt. Sowohl der Originalname (nicht normalisiert) als auch der Filename werden gespeichert. Die Files können nicht verwaltet werden. Die Ausgabe kann über einen Media Effekt vorgenommen werden.

Um das Multiuploadfeld im Frontend nutzen zu können, müssen noch folgende Zeilen ins REDAXO Template vor den schließendene body.

```
<?php if (rex_ycom_auth::getUser()) : ?>
<script src="/assets/addons/ycom_board/uploadfile/ycom-board-file-upload.js"></script>
<?php endif ?>
```

In den Head noch:
```
<?php if (rex_ycom_auth::getUser()) : ?>
<link rel="stylesheet" href="<?= rex_url::assets('/addons/ycom_board/uploadfile/jquery-file-upload.min.css') ?>">
<?php endif ?>
```

Das Multiuploadfeld wird nur bei eingeloggten YCom Usern benötigt.

Fragmente
---------

Die Frontendausgabe des Boards wird über Fragmente realisiert. Die Fragmente liegen im Addon Verzeichnis fragments. Wenn eine eigene Ausgabe realisiert werden soll, empfiehlt es sich die Fragmente in ein eigenes Fragments Verzeichnis zu kopieren und zu modifizieren. z.B. in das Projekt AddOn oder ins Theme. Mehr dazu in der REDAXO Doku.

Credits
-------

Die Portierung des AddOns von R4 nach R5 wurde zum großen Teil finanziert durch
Polarpixel
Thomas Rotzek