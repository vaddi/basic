<?php

$body = '<!DOCTYPE html>
  <html lang="en">
    <head>
      <title>Nachricht von '. $name .'</title>
      <meta http-equiv="content-type" content="text/html; charset=utf-8">
    
      <style>
  			/* Client-specific Styles */
  			#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
  			body{background: #CCCCCC; color: #000000; width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
  			body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */

  			/* Reset Styles */
  			body{margin:0; padding:0;}
  			img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
  			table td{border-collapse:collapse;}
  			#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}

  			/* Template Styles */

  			body {
  				background-color:#FAFAFA;
          text-align: center;
  			}

        #backgroundTable {
          width: 100%;
          height: 100%;
          border: none;
          border-spacing: 0;
        }
        #backgroundTable td,
        #backgroundTable th {
          padding: 0;
          text-align: center;
          vertical-align: middle;
        }

  			#templateContainer {
          border: 1px solid #DDDDDD;
          border-spacing: 0;
          width: 600px;
          background: #FFFFFF;
  			}
        #templateContainer td,
        #templateContainer th {
          padding: 0;
          text-align: center;
          vertical-align: top;
        }

  			h2, .h2{
  				color:#202020;
  				display:block;
  				font-family:Arial;
  				font-size:30px;
  				font-weight:bold;
  				line-height:100%;
  				margin-top:0;
  				margin-right:0;
  				margin-bottom:10px;
  				margin-left:0;
  				text-align:left;
  			}
        
        h2 small:before { 
          content: "\A"; 
          white-space: pre; 
          color:#ccc; 
        }

        h2 small {
          color: #ccc;
        }

  			#templatePreheader {
  				background-color:#FAFAFA;
          border-spacing: 0;
          width: 600px;
          border: none;
  			}
        #templatePreheader td,
        #templatePreheader th {
          padding: 10px;
        }

        .preheaderContent {
          vertical-align: top;
        }

  			.preheaderContent div{
  				color:#505050;
  				font-family:Arial;
  				font-size:10px;
  				line-height:100%;
  				text-align:left;
  			}

  			.preheaderContent div a:link, .preheaderContent div a:visited, /* Yahoo! Mail Override */ .preheaderContent div a .yshortcuts /* Yahoo! Mail Override */{
  				color:#336699;
  				font-weight:normal;
  				text-decoration:underline;
  			}

  			#templateHeader{
  				background-color:#FFFFFF;
  				border-bottom:0;
  			}

  			.headerContent{
  				color:#202020;
  				font-family:Arial;
  				font-size:34px;
  				font-weight:bold;
  				line-height:100%;
  				padding:0;
  				text-align:center;
  				vertical-align:middle;
  			}

  			.headerContent a:link, .headerContent a:visited, /* Yahoo! Mail Override */ .headerContent a .yshortcuts /* Yahoo! Mail Override */{
  				color:#336699;
  				font-weight:normal;
  				text-decoration:underline;
  			}

  			#headerImage{
  				height:auto;
  				max-width:600px !important;
          max-width: 600px;
          max-height: 150px;
          border: none;
          width: 600px;
          height: 150px;
  			}

  			#templateContainer, .bodyContent{
  				background-color:#FFFFFF;
  			}

        .bodyContent {
          vertical-align: top;
        }
  			.bodyContent div{
  				color:#505050;
  				font-family:Arial;
  				font-size:14px;
  				line-height:150%;
  				text-align:left;
  			}

  			.bodyContent div a:link, .bodyContent div a:visited, /* Yahoo! Mail Override */ .bodyContent div a .yshortcuts /* Yahoo! Mail Override */{
  				color:#336699;
  				font-weight:normal;
  				text-decoration:underline;
  			}

  			.bodyContent img{
  				display:inline;
  				height:auto;
  			}

  			#templateFooter {
  				background-color:#FFFFFF;
  				border: none;
          width: 100%;
          border-spacing: 0;
  			}
        #templateFooter td,
        #templateFooter th {
          padding: 10px;
        }

        .footerContent {
          vertical-align: top;
        }
  			.footerContent div{
  				color:#707070;
  				font-family:Arial;
  				font-size:12px;
  				line-height:125%;
  				text-align:left;
  			}

  			.footerContent div a:link, .footerContent div a:visited, /* Yahoo! Mail Override */ .footerContent div a .yshortcuts /* Yahoo! Mail Override */{
  				color:#336699;
  				font-weight:normal;
  				text-decoration:underline;
  			}

  			.footerContent img{
  				display:inline;
  			}

  			#social{
  				background-color:#FAFAFA;
  				border:0;
  			}

  			#social div{
  				text-align:center;
          vertical-align: middle;
  			}

        #social .innerSocial{
          text-align: center;
        }

  			#utility{
  				background-color:#FFFFFF;
  				border:0;
  			}

  			#utility div{
  				text-align:center;
          vertical-align: middle;
  			}
      
        .right {
          float: right;
        }
      
        .moz-signature {
          text-align: center;
          width: 600px;
          margin: auto;
        
        }
      
        #templatePreheaderSub {
          border: none;
          border-spacing: 0;
          width: 100%;
        }
        #templatePreheaderSub td,
        #templatePreheaderSub th {
          padding: 10px;
          vertical-align: top;
        }
      
        #templateHeader {
          border: none;
          border-spacing: 0;
          width: 600px;
        }
        #templateHeader td,
        #templateHeader th {
          padding: 0;
        }
      
        #templateBody {
          border-spacing: 0;
          width: 600px;
          border: none;
        }
        #templateBody td,
        #templateBody th {
          padding: 0;
        }

        #templateInnerBody {
          border-spacing: 0;
          width: 100%;
          border: none;
        }
        #templateInnerBody td,
        #templateInnerBody th {
          padding: 20px;
          vertical-align: top;
        }
      
        #innerImage {
          max-width: 256px;
          margin: 0 0 15px 15px;
          float: right;
          height: 128px;
          width: 128px;
        }
      
        .rightElements {
          width: 190px;
        }
      
        .rightElements .vtop {
          vertical-align: top;
        } 

        .textJustify {
          text-align: justify;
        }
      
        .footerContent table {
          border: 0;
          width: 100%;
          border-spacing: 0;
        }
        .footerContent table td,
        .footerContent table th {
          padding: 10px;
          vertical-align: top;
        }
        .leftArea {
          vertical-align: top;
          width: 350px;
        }

  		</style>
    </head>
  
    <body>

      <div class="moz-signature">

          <table id="backgroundTable">
          
            <tbody>
              <tr>
                <td>

                  <table id="templatePreheader">
                    <tbody>
                      <tr>

                      <td class="preheaderContent">

                        <table id="templatePreheaderSub">
                          <tbody>
                            <tr>
                              <td>
                                <div>
                                  Webformular Nachricht von '. $name .'
                                </div>
                              </td>
                              <td class="rightElements">
                                <div class="right">
                                  <a href="'. $company_url .'" target="_blank">'. $company_name .'</a>
  				                        </div>
                                </td>
                              </tr>
                            </tbody>
                          </table>

                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <table id="templateContainer">
                    <tbody>
                      <tr>
                        <td>

                          <table id="templateHeader">
                            <tbody>
                              <tr>
                                <td class="headerContent">

                                <a href="'. $company_url .'">
				                          <img
                                    alt="Logo"
                                    src="'. $img_header .'"
                                    id="headerImage">
				                        </a>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      
                      </td>
                    </tr>
                    <tr>
                      <td>

                        <table id="templateBody">
                          <tbody>
                            <tr>
                              <td class="bodyContent">

                                <table id="templateInnerBody">
                                  <tbody>
                                    <tr>
                                      <td>
                                        <div>
					                                <img
                                            id="innerImage"
                                            src="'. $img_intext .'"
                                            alt="Logo">
                                          <h2>'. $subject .'</h2>
                                          <div class="textJustify">
                                            '. $comments .'
                                          </div>
                                          <br>
                                          <br>
                                          Webformular abgesendet von<br>
                                          '. $name .' &lt;' . $email . '&gt;<br>
                                          </div>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>

                                </td>
                              </tr>
                            </tbody>
                          </table>

                        </td>
                      </tr>
                      <tr>
                        <td>

                          <table id="templateFooter">
                            <tbody>
                              <tr>
                                <td class="footerContent">

                                  <table>
                                    <tbody>
                                      <tr>
                                        <td colspan="2" id="social">
                                        <div>
                                          '. $twitter_url .'
                                          '. $facebook_url .'
                                          <a href="mailto:'.$email.'">Antwort an '.$name.'</a>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td class="leftArea">
                                        <div>
                                          ' . $company_string .'
                                          ' . $company_phone . '
                                          ' . $company_fax . '
                                          ' . $company_mail . '
                                          <strong>Internet:</strong> <a href="'.$company_url.'">'.$company_name.'</a>
                                          <br>
                                        </div>
                                      </td>
                                      <td class="rightElements vtop">
                                        <div class="right">
					                                ' . $slogan . '
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td colspan="2" id="utility">
                                        <div>
                                          Versendet am '. $datum .' um '. $uhrzeit .' <br>von '. $ip .' ('. $host .').
                                        </div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>

                              </td>
                            </tr>
                          </tbody>
                        </table>

                      </td>
                    </tr>
                  </tbody>
                </table>

                <br>
              </td>
            </tr>
          </tbody>
        </table>

    </div>
  </body>
</html>
';

?>
