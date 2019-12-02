YCom Board für REDAXO 5.8
=========================


Beschreibung
------------
Das Board ist ein einfaches Plugin, um ein einfaches Messageboard für die YCom zu realisieren. Das AddOn wurde von einer 4er Version migriert.


Installation
------------

* Die Dateien in das Verzeichnis redaxo/src/addons/ycom/plugin/board kopieren
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

In der YCom muss eine Gruppe erstellt werden, die als Board Admin Gruppe im Modul für jedes Board festgelegt werden kann. (Momentan noch ohne Funktion).


Credits
-------

Die Portierung des AddOns von R4 nach R5 wurde zum großen Teil finanziert durch Polarpixel und Thomas Rotzek
