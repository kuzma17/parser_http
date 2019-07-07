
## Parser_http
This is a script for parsing the Internet resources.<br>
The script writes the results to a file csv.<br>

#### Commands

<strong>parse</strong> 
 url - Enter parse url address for parsing.<br>
<strong>report</strong> 
 domain - Enter the domain for which you previously parsed<br>
<strong>report</strong> 
 domain: all - f you enter all get a list of all parsed domains<br>
<strong>help</strong> 
 The Help

#### Information

If you need to modify the parser, you need to inherit from the abstract <strong>class Parser</strong> 
and implement the <strong>parseItems()</strong> method.<br>
To output the results, it is necessary to inherit from the abstract <strong>class Reports</strong> 
and implement the <strong>getItems()</strong> method.<br>