# User Guide für das Elastic Export Guenstiger.de Plugin

<div class="container-toc"></div>

## 1 Bei Guenstiger.de registrieren

Guenstiger.de ist ein Preisvergleichsportal, das neben Preisvergleichen auch Nutzermeinungen, Testberichte und Händlerbewertungen anbietet.

## 2 Das Format Guenstiger-Plugin in plentymarkets einrichten

Um dieses Format nutzen zu können, benötigen Sie das Plugin Elastic Export.

Auf der Handbuchseite [Daten exportieren](https://knowledge.plentymarkets.com/basics/datenaustausch/daten-exportieren#30) werden allgemein die einzelnen Formateinstellungen beschrieben.

In der folgenden Tabelle finden Sie spezifische Hinweise zu den Einstellungen, Formateinstellungen und empfohlenen Artikelfiltern für das Format **Guenstiger-Plugin**.
<table>
    <tr>
        <th>
            Einstellung
        </th>
        <th>
            Erläuterung
        </th>
    </tr>
    <tr>
        <th colspan="2">
            Einstellungen
        </th>
    </tr>
    <tr>
        <td>
            Format
        </td>
        <td>
            Das Format <b>GuenstigerDE-Plugin</b> wählen.
        </td>
    </tr>
    <tr>
        <td>
            Provisioning
        </td>
        <td>
            <b>URL</b> wählen.
        </td>
    </tr>
    <tr>
        <td>
            Dateiname
        </td>
        <td>
            Der Dateiname muss auf <b>.csv</b> enden, damit Guenstiger.de die Datei erfolgreich importieren kann.
        </td>
    </tr>
    <tr>
        <th colspan="2">
            Artikelfilter
        </th>
    </tr>
    <tr>
        <td>
            Aktiv
        </td>
        <td>
            <b>Aktiv</b> wählen.
        </td>
    </tr>
    <tr>
        <td>
            Märkte
        </td>
        <td>
            Eine <b>Herkunft</b> wählen.
        </td>
    </tr>
    <tr>
        <th colspan="2">
            Formateinstellungen
        </th>
    </tr>
    <tr>
        <td>
            Bild
        </td>
        <td>
            <b>Erstes Bild</b> wählen.
        </td>
    </tr>
    <tr>
        <td>
            MwSt.-Hinweis
        </td>
        <td>
            Diese Option ist für dieses Format nicht relevant.
        </td>
    </tr>
</table>

## 3 Übersicht der verfügbaren Spalten
<table>
    <tr>
        <th>
            Spaltenbezeichnung
        </th>
        <th>
            Erläuterung
        </th>
    </tr>
    <tr>
        <td>
            EAN
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Entsprechend der Formateinstellung <b>Barcode</b>.
        </td>
    </tr>
    <tr>
        <td>
            ISBN
        </td>
        <td>
            Die <b>ISBN</b> einer Variante.
        </td>
    </tr>
    <tr>
        <td>
            HerstellerArtNr
        </td>
        <td>
            Das <b>Model</b> der Variante.
        </td>
    </tr>
    <tr>
        <td>
            Hersteller
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Der <b>Name des Herstellers</b> der Variante. Der <b>Externe Name</b> unter <b>Einstellungen » Artikel » Hersteller</b> wird bevorzugt, wenn vorhanden.
        </td>
    </tr>
    <tr>
        <td>
            Produktname
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Entsprechend der Formateinstellung <b>Artikelname</b>.
        </td>
    </tr>
    <tr>
        <td>
            Beschreibung
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Entsprechend der Formateinstellung <b>Beschreibung</b>.
        </td>
    </tr>
    <tr>
        <td>
            Preis
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Der <b>Verkaufspreis</b> der Variante.
        </td>
    </tr>
    <tr>
        <td>
            Klick-Out-URL
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Die <b>Produkt-URL</b> der Variante, abhängig der Formateinstellung <b>Produkt-URL</b> und <b>URL-Parameter</b>.
        </td>
    </tr>
    <tr>
        <td>
            Kategorie
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Die <b>Standardkategorie</b> der Variante.
        </td>
    </tr>
    <tr>
        <td>
            Bild-URL
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            Das <b>Hauptbild</b> der Variante, abhängig der Formateinstellung <b>Bild</b>.
        </td>
    </tr>
    <tr>
        <td>
            Lieferzeit
        </td>
        <td>
            Die durchschnittliche <b>Lieferzeit in Tagen</b> der Variante.
        </td>
    </tr>
    <tr>
        <td>
            Lieferkosten
        </td>
        <td>
            Die <b>Lieferkosten</b> der Variante.
        </td>
    </tr>
    <tr>
        <td>
            Grundpreis
        </td>
        <td>
            Der <b>Grundpreis</b> der Variante.
        </td>
    </tr>
</table>

## 4 Lizenz
Das gesamte Projekt unterliegt der GNU AFFERO GENERAL PUBLIC LICENSE – weitere Informationen finden Sie in der [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-guenstiger-de/blob/master/LICENSE.md).
