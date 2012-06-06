### dbMultipleChoice

dbMultipleChoice enable you to create multiple choice tests for e-Learning with [WebsiteBaker] [1] or [LEPTON CMS] [2].

#### Requirements

* PHP 5.2.x or newer
* use of [WebsiteBaker] [1] _or_ [LEPTON CMS] [2]
* Add-on [dbConnect_LE] [3] installed
* Add-on [rhTools] [4] installed
* Add-on [Dwoo] [5] installed 

#### Installation

* download the actual [dbMultipleChoice_x.xx.zip] [6] installation archive
* in CMS backend select the file from "Add-ons" -> "Modules" -> "Install module"

#### First Steps

In your CMS select Admin-Tools -> dbMultipleChoice.

To create a new questionaire select the tab "Edit Questionaire" and start a new multiple choice test. Define the **name** (will be later used to select this test), give it a **title** and **description**, select the **default** group and save the new questionaire before you go ahead.

Now you need some Questions for your first Multiple Choice test. Select the tab "Questions" and "Edit Question" to create a new question. Fill in the **name** and **question**, describe the question, select a group and create the possible answers. At least you can define responses to correct, wrong and partially answered questions. Repeat this step to gather a couple of questions.

Go back to "Questionaires". Select your Questionaire from the list. Now you can select the questions you have created and assign them to the questionaire. Save the test. 

In the list of the Questionaires you will see the ID of your first multiple choice test. Remember this ID.

Create or edit a WYSIWYG page and insert the following Droplet call:

    [[mc_questionaire?id=<ID>]]
    
Replace &lt;ID> with the ID of your test and save the page.    

That's all!

You will find more informations about dbMultipleChoice at [phpManufaktur] [7].

Please use the [General Addons Support] [8] of the phpManufaktur to get support. 

[1]: http://websitebaker2.org
[2]: http://lepton-cms.org
[3]: https://github.com/phpManufaktur/dbConnect_LE/downloads
[4]: https://github.com/phpManufaktur/rhTools/downloads
[5]: https://github.com/phpManufaktur/Dwoo/downloads
[6]: https://github.com/phpManufaktur/dbMultipleChoice/downloads
[7]: http://phpmanufaktur.de
[8]: https://phpmanufaktur.de/support