# Elastic Export Guenstiger.de Plugin user guide

<div class="container-toc"></div>

## 1 Registering with Guenstiger.de

Guenstiger.de is a German price comparison portal that also offers customer opinions, test reports and seller reviews. Please note that this website is currently only available in German.

## 2 Setting up the data format GuenstigerDE-Plugin in plentymarkets

To use this format, you need the Elastic Export plugin.

Refer to the [Exporting data formats for price search engines](https://knowledge.plentymarkets.com/en/basics/data-exchange/exporting-data#30) page of the manual for further details about the individual format settings.

The following table lists details for settings, format settings and recommended item filters for the format **GuenstigerDE-Plugin**.
<table>
    <tr>
        <th>
            Settings
        </th>
        <th>
            Explanation
        </th>
    </tr>
    <tr>
        <th colspan="2">
            Settings
        </th>
    </tr>
    <tr>
        <td>
            Format
        </td>
        <td>
            Choose the format <b>GuenstigerDE-Plugin</b>.
        </td>
    </tr>
    <tr>
        <td>
            Provisioning
        </td>
        <td>
            Choose <b>URL</b>.
        </td>
    </tr>
    <tr>
        <td>
            File name
        </td>
        <td>
            The file name must have the ending <b>.csv</b> for Guenstiger.de to be able to import the file successfully.
        </td>
    </tr>
    <tr>
        <th colspan="2">
            Item filter
        </th>
    </tr>
    <tr>
        <td>
            Active
        </td>
        <td>
            Choose <b>Active</b>.
        </td>
    </tr>
    <tr>
        <td>
            Markets
        </td>
        <td>
            Choose a <b>referrer</b>.
        </td>
    </tr>
    <tr>
        <th colspan="2">
            Format settings
        </th>
    </tr>
    <tr>
        <td>
            Image
        </td>
        <td>
            Choose <b>First image</b>.
        </td>
    </tr>
    <tr>
        <td>
            VAT note
        </td>
        <td>
            This option does not affect this format.
        </td>
    </tr>
</table>

## 3 Overview of available columns
<table>
    <tr>
        <th>
            Column description
        </th>
        <th>
            Explanation
        </th>
    </tr>
    <tr>
        <td>
            EAN
        </td>
        <td>
            <b>Required</b><br>
            According to the format setting <b>Barcode</b>.
        </td>
    </tr>
    <tr>
        <td>
            ISBN
        </td>
        <td>
            The <b>ISBN</b> of the variation.
        </td>
    </tr>
    <tr>
        <td>
            HerstellerArtNr
        </td>
        <td>
            The <b>model</b> of the variation.
        </td>
    </tr>
    <tr>
        <td>
            Hersteller
        </td>
        <td>
            <b>Required</b><br>
            The <b>name of the manufacturer</b> of the item. The <b>external name</b> within <b>Settings » Items » Manufacturer</b> will be preferred if existing.
        </td>
    </tr>
    <tr>
        <td>
            Produktname
        </td>
        <td>
            <b>Required</b><br>
            According to the format setting <b>Item name</b>.
        </td>
    </tr>
    <tr>
        <td>
            Beschreibung
        </td>
        <td>
            <b>Required</b><br>
            According to the format setting <b>Description</b>.
        </td>
    </tr>
    <tr>
        <td>
            Preis
        </td>
        <td>
            <b>Required</b><br>
            The <b>retail price</b> of the variation.
        </td>
    </tr>
    <tr>
        <td>
            Klick-Out-URL
        </td>
        <td>
            <b>Required</b><br>
            The <b>product URL</b> of the variation, depending on the format setting <b>product URL</b> and <b>URL parameter</b>.
        </td>
    </tr>
    <tr>
        <td>
            Kategorie
        </td>
        <td>
            <b>Required</b><br>
            The <b>default category</b> of the variation.
        </td>
    </tr>
    <tr>
        <td>
            Bild-URL
        </td>
        <td>
            <b>Required</b><br>
            The <b>main image</b> of the variation, depending on the format setting <b>Image</b>.
        </td>
    </tr>
    <tr>
        <td>
            Lieferzeit
        </td>
        <td>
            The average <b>delivery time in days</b> of the variation.
        </td>
    </tr>
    <tr>
        <td>
            Lieferkosten
        </td>
        <td>
            The <b>shipping costs</b> of the variation.
        </td>
    </tr>
    <tr>
        <td>
            Grundpreis
        </td>
        <td>
            The <b>base price</b> of the variation.
        </td>
    </tr>
</table>

## 4 License
This project is licensed under the GNU AFFERO GENERAL PUBLIC LICENSE.- find further information in the [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-guenstiger-de/blob/master/LICENSE.md).