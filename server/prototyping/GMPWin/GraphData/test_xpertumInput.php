<?php

/*
	 * Copyright (c) 2015, CEDEP France,
 	 * Authors: Albert A. Angehrn, Marco Luccini, Pradeep Kumar Mittal
         * All rights reserved.
	 * Redistribution and use in source and binary forms, with or without modification, 
	 * are permitted provided that the following conditions are met:
	 *
	 *  * Redistributions of source code must retain the above copyright notice, 
	 *    this list of conditions and the following disclaimer. 
	 *  * Redistributions in binary form must reproduce the above copyright notice, 
	 *    this list of conditions and the following disclaimer in the documentation
	 *    and/or other materials provided with the distribution. 
	 *  * Neither the name of the COLLAGE Group nor the names of its 
	 *    contributors may be used to endorse or promote products derived from this 
	 *    software without specific prior written permission. 
	 *
	 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
	 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	 * DISCLAIMED. IN NO EVENT SHALL CONSORTIUM BOARD COLLAGE Group BE LIABLE FOR ANY
	 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

?>


<?php
// the next 2 lines to prevent caching
session_start();
header("Cache-control: private");


header("Content-type: text/xml");
	
	
	include "conntube.php";
    
	$fname=$_GET['value'];
	echo $fname;
      $fname="new_xpertumInput.xml";
	
	$fid=fopen($fname,"w");
//	echo $fid;
    $retstring="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n\n<competences>\n\n";  
	fputs($fid,"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n\n<competences>\n\n");
	
    	
	//to put the affiliationtypes and corresponding affiliations
	 $affiliation_rs=mysql_query("select * from affiliationtable");
	 while($affiliation_row=mysql_fetch_array($affiliation_rs))
	 {
	       $affiliationtype_name=$affiliation_row['name'];
		   $affiliationtype_id=$affiliation_row['id'];
		   $competencesList=$affiliation_row['competences'];
		   $retstring=$retstring."<affiliationtype id=\"$affiliationtype_id\"  name=\"$affiliationtype_name\">\n";
		   fputs($fid,"<affiliationtype id=\"$affiliationtype_id\"  name=\"$affiliationtype_name\">\n");
		      
			  // echo $competenceslist;
		   $competenceToken = strtok($competencesList, ",");
		   while ($competenceToken !== false)
           {
		        $result=mysql_query("select name from competencetable where id='$competenceToken'");
                while ( $competenceRow = mysql_fetch_array($result) )
				{
	                   	 $competenceName		= 	$competenceRow['name'];
						 $retstring=$retstring."<affiliation id=\"$competenceToken\" name=\"$competenceName\"/>\n";
			              fputs($fid,"<affiliation id=\"$competenceToken\" name=\"$competenceName\"/>\n"); 		
	            }
			
                $competenceToken = strtok(",");
           }

		   
		   $retstring=$retstring."</affiliationtype>\n\n";
		   fputs($fid,"</affiliationtype>\n\n");
	 }
	
	
	$echostr="";

	$rs				=	mysql_query ("Select * from peoplenodes");
    $id=0;
   while ($row = mysql_fetch_array($rs))
   {
		$retstring=$retstring."<person\n";
		fputs($fid,"<person\n");
		$name=$row['name'];
		$space_pos=stripos($name," ");
		if($space_pos==false)
		{
           $firstName=$name;
           $lastName=$name;
        }
        else
        {		
		 $firstName=substr($name,0,$space_pos);
		 $lastName=substr($name,$space_pos+1);
		}
		//	firstname="Krishna"
		//lastname="Mahesh"
		$alias=$firstName;
		$email=$row['id'];
		$jobrole=$row['title'];
		$organization=$row['company'];
		$region=$row['location'];
		$country=$row['nationality'];
		$timezone="GTM (+01)";
		$pictname=$row['picture'];
        $comp_strengths_list=$row['competences']; 	
		$id=$id+1;
	//	echo $id;
		$retstring=$retstring."id=\"$id\"\nfirstname=\"$firstName\"\nlastname=\"$lastName\"\nalias=\"$alias\"\nemail=\"$email\"\njobrole=\"$jobrole\"\norganization=\"$organization\"\nphone=\"-\" \nregion=\"$region\"\npostalcode=\"77305\"\ncountry=\"$country\"\ntimezone=\"$timezone\"\npictname=\"$pictname\">\n";
		fputs($fid,"id=\"$id\"\nfirstname=\"$firstName\"\nlastname=\"$lastName\"\nalias=\"$alias\"\nemail=\"$email\"\njobrole=\"$jobrole\"\norganization=\"$organization\"\nphone=\"-\" \nregion=\"$region\"\npostalcode=\"77305\"\ncountry=\"$country\"\ntimezone=\"$timezone\"\npictname=\"$pictname\">\n");
	         
        $comp_strength_token = strtok($comp_strengths_list, ",");
		 
		   while ($comp_strength_token !== false)
           {
		   
		            $pos=stripos($comp_strength_token,"?");
		            $competenceId=substr($comp_strength_token,0,$pos);
					$competenceStrength=substr($comp_strength_token,$pos+1) / 100;
				//	echo $competenceId."--".$competenceStrength."</br>";
					$retstring=$retstring."<affiliation id=\"$competenceId\" strength=\"$competenceStrength\"/>\n";
			        fputs($fid,"<affiliation id=\"$competenceId\" strength=\"$competenceStrength\"/>\n");
			
			/*	    
		        $result=mysql_query("select name from competencetable where id='$competenceId'");
              
			   while ( $competenceRow = mysql_fetch_array($result) )
				{
	                   	 $competenceName		= 	$competenceRow['name'];
			              fputs($fid,"<affiliation id=\"$competenceI\" name=\"$competenceName\"/>\n"); 		
	                               }
			*/
                $comp_strength_token = strtok(",");
           }
	
	//     $echostr=$echostr. $name."\n".$alias."\n".$email."\n".$jobrole."\n".$organization."\n".$region."\n".$country."\n".$pictname."\n";
		 $retstring=$retstring."\n</person>\n\n";
	     fputs($fid,"\n</person>\n\n");

   }

	

	
	
      $retstring=$retstring."</competences>";
	 fputs($fid,"</competences>");
	echo $retstring;
	fclose($fid);
	?>