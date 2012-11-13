
Contao Extension "formmails"
============================

## Intro ##

This is an extension for the [Contao Open Source CMS][1]. If you are not a software developer, please download or install from the [Contao Extension Repository][2].


## Description ##

This extension builds on top of the form generator. The submitted form data can be sent to multiple email addresses (e.g. one email to the user, one to the administrator).


## Usage ##

Create one or multiple email templates in the [mailtemplates][4] extension. Each can have multiple languages (the correct one will be taken depending on the page language). You can use standard insert tags plus [simple tokens][5] for all fields to add form data into the template.


## Dependencies ##

You must install the following extensions to make it work.
- [MultiColumnWizard][3] by Andreas Isaak (menatwork)
- [mailtemplates][4] by Andreas Schempp (terminal42)



[1]: http://contao.org/
[2]: http://contao.org/en/extension-list.html
[3]: https://github.com/menatwork/MultiColumnWizard
[4]: https://github.com/aschempp/contao-mailtemplates
[5]: http://contao.org/en/newsletters.html#personalize