# Guenstiger.de Plugin user guide

<div class="container-toc"></div>

## 1 Registering with Guenstiger.de

Guenstiger.de is a German price comparison portal that also offers customer opinions, test reports and seller reviews. Please note that this website is currently only available in German.

## 2 Setting up the data format GuenstigerDE-Plugin in plentymarkets

By installing this plugin you will receive the export format **GuenstigerDE-Plugin**. Use this format to exchange data between plentymarkets and Guenstiger.de. It is required to install the Plugin **Elastic export** from the plentyMarketplace first before you can use the format **GuenstigerDE-Plugin** in plentymarkets.

Once both plugins are installed, you can create the export format **GuenstigerDE-Plugin**. Refer to the [Exporting data formats for price search engines](https://knowledge.plentymarkets.com/en/basics/data-exchange/exporting-data#30) page of the manual for further details about the individual format settings.

Creating a new export format:

1. Go to **Data » Elastic export**.
2. Click on **New export**.
3. Carry out the settings as desired. Pay attention to the information given in table 1.
4. **Save** the settings. 
→ The export format will be given an ID and it will appear in the overview within the **Exports** tab.

The following table lists details for settings, format settings and recommended item filters for the format GuenstigerDE-Plugin.

| **Setting**                                           | **Explanation** |
| :---                                                  | :--- |
| **Settings**                                          | 
| **Name**                                              | Enter a name. The export format will be listed under this name in the overview within the **Exports** tab. |
| **Type**                                              | Select the type **Item** from the drop-down list. |
| **Format**                                            | Select **GuenstigerDE-Plugin**. |
| **Limit**                                             | Enter a number. If you want to transfer more than 9,999 data records to the price search engine, then the output file will not be generated again for another 24 hours. This is to save resources. If more than 9,999 data records are necessary, the setting **Generate cache file** has to be active. |
| **Generate cache file**                               | Place a check mark if you want to transfer more than 9,999 data records to the price search engine. The output file will not be generated again for another 24 hours. We recommend not to activate this setting for more than 20 export formats. This is to save resources. |
| **Provisioning**                                      | Select **URL**. This option generates a token for authentication in order to allow external access. |
| **File name**                                         | The file name must have the ending **.csv** for Guenstiger.de to be able to import the file successfully. |
| **Token, URL**                                        | If you have selected the option **URL** under **Provisioning**, then click on **Generate token**. The token will be entered automatically. When the token is generated under **Token**, the URL is entered automatically. |
| **Item filters**                                      |
| **Add item filters**                                  | Select an item filter from the drop-down list and click on **Add**. There are no filters set in default. It is possible to add multiple item filters from the drop-down list one after the other.<br/> **Variations** = Select **Transfer all** or **Only transfer main variations**.<br/> **Markets** = Select one market, several or **ALL**.<br/> The availability for all markets selected here has to be saved for the item. Otherwise, the export will not take place.<br/> **Currency** = Select a currency.<br/> **Category** = Activate to transfer the item with its category link. Only items belonging to this category will be exported.<br/> **Image** = Activate to transfer the item with its image. Only items with images will be transferred.<br/> **Client** = Select client.<br/> **Stock** = Select which stocks you want to export.<br/> **Flag 1 - 2** = Select the flag.<br/> **Manufacturer** = Select one, several or **ALL** manufacturers.<br/> **Active** = Only active variations will be exported. |
| **Format settings**                                   |
| **Product URL**                                       | Choose wich URL should be transferred to the price comparison portal, the item’s URL or the variation’s URL. Variation SKUs can only be transferred in combination with the Ceres store. |
| **Client**                                            | Select a client. This setting is used for the URL structure. |
| **URL parameter**                                     | Enter a suffix for the product URL if this is required for the export. If you have activated the transfer option for the product URL further up, then this character string will be added to the product URL. |
| **Order referrer**                                    | Choose the order referrer that should be assigned during the order import from the drop-down list. |
| **Marketplace account**                               | Select the market account from the drop-down list. The selected referrer is added to the product URL so that sales can be analysed later. |
| **Language**                                          | Select the language from the drop-down list. |
| **Item name**                                         | Select **Name 1**, **Name 2** or **Name 3**. These names are saved in the **Texts** tab of the item. Enter a number into the **Maximum number of characters (def. Text)** field if desired. This specifies how many characters should be exported for the item name. |
| **Preview text**                                      | Select the text that you want to transfer as preview text.<br/> Enter a number into the **Maximum number of characters (def. text)** field if desired. This will specify how many characters should be exported for the item name.<br/> Activate the option **Remove HTML tags** if you want HTML tags to be removed during the export.<br/> If you only want to allow specific HTML tags to be exported, then enter these tags into the field **Permitted HTML tags, separated by comma (def. Text)**. Use commas to separate multiple tags. |
| **Description**                                       | Select the text that you want to transfer as description.<br/> Enter a number into the **Maximum number of characters (def. text)** field if desired. This will specify how many characters should be exported for the description.<br/> Activate the option **Remove HTML tags** if you want HTML tags to be removed during the export.<br/> If you only want to allow specific HTML tags to be exported, then enter these tags into the field **Permitted HTML tags, separated by comma (def. Text)**. Use commas to separate multiple tags. |
| **Target country**                                    | Select the target country from the drop-down list. |
| **Barcode**                                           | Select the ASIN, ISBN or an EAN from the drop-down list. The barcode has to be linked to the order referrer selected above. If the barcode is not linked to the order referrer it will not be exported. |
| **Image**                                             | Select **First image**. |
| **Image position of the energy efficiency label**     | Enter the position. Every image that should be transferred as an energy efficiency label must have this position. |
| **Stockbuffer**                                       | This option does not affect this format. |
| **Stock for variations without stock limitation**     | This option does not affect this format. |
| **Stock for variations with no stock administration** | This option does not affect this format. |
| **Retail price**                                      | Select gross price or net price from the drop-down list. |
| **Offer price**                                       | Activate to transfer the offer price |
| **RRP**                                               | Activate to transfer the RRP. |
| **Shipping costs**                                    | Activate this option if you want to use the shipping costs that are saved in a configuration. If this option is activated, then you will be able to select the configuration and the payment method from the drop-down lists.<br/> Activate the option **Transfer flat rate shipping charge** if you want to use a fixed shipping charge. If this option is activated, a value has to be entered in the line underneath. |
| **VAT note**                                          | This option does not affect this format. |
| **Item availability**                                 | Activate the **overwrite** option and enter item availabilities into the fields **1** to **10**. The fields represent the IDs of the availabilities. This will overwrite the item availabilities that are saved in the menu **System » Item » Availability**. |

## 3 Available columns for the export file

| **Column description** | **Explanation** |
| :---                   | :--- |
| EAN                    | **Required**<br/> According to the format setting **Barcode**. |
| ISBN                   | The **ISBN** of the variation. |
| HerstellerArtNr        | The **model** of the variation. |
| Hersteller             | **Required**<br/> The **name of the manufacturer** of the item. The **external name** within **Systms » Items » Manufacturers** will be preferred if existing. |
| Produktname            | **Required**<br/> According to the format setting **Item name**. |
| Beschreibung           | **Required**<br/> According to the format setting **Description**. |
| Preis                  | **Required**<br/> The **retail price** of the variation. |
| Klick-Out-URL          | **Required**<br/> The **product URL** of the variation, depending on the format setting **product URL** and **URL parameter**. |
| Kategorie              | **Required**<br/> The **default category** of the variation. |
| Bild-URL               | **Required**<br/> The **main image** of the variation, depending on the format setting **Image**. |
| Lieferzeit             | The **average delivery time in days** of the variation. |
| Lieferkosten           | The **shipping costs** of the variation. |
| Grundpreis             | The **base price** of the variation. |

## 4 License
This project is licensed under the GNU AFFERO GENERAL PUBLIC LICENSE.- find further information in the [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-guenstiger-de/blob/master/LICENSE.md).