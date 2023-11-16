<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html lang="en">
    <head>
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.11.2/css/all.css" integrity="sha384-zrnmn8R8KkWl12rAZFt4yKjxplaDaT7/EUkKm7AovijfrQItFWR7O/JJn4DAa/gx" crossorigin="anonymous">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./css/menustyle.css"/>
	<link rel="stylesheet" href="./css/style.css"/>
    <link rel="stylesheet" href="./css/moviestyle.css"/>
	<link rel="stylesheet" href="./css/indexstyle.css"/>
    <link rel="stylesheet" media="only screen and (max-width: 740px)" href="./css/mobilestyle.css"/>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <title>Add Movie</title>
    </head>
	
    <body>
	<?php
    $adminpage=true;
	require 'curtains.php';//Calls curtains.php file
	?>
		<div class="sections">
		<div class="section gap">
        <div class="movie-data">
		<div class="mtitle">
		<strong style="color:#000">Title </strong><input class="inputs" type="text" placeholder="Enter Title" id="movietitle" >
		</div>
		<strong>Preview </strong><br>
		<div class="mpreview" id="previewdiv">
			
		</div>
		<input class="inputs" type="file" accept="image/x-png,image/gif,image/jpeg" placeholder="Upload Image" id="imageup1">
		<button onclick="uploadImage(1)" class="sidebutton">Change Preview Image</button>
		<div class="moviedata">

			<strong>Premier </strong><input class="inputs" id="reldate" type="date" placeholder="Enter Date" id="premierdate" >
			<strong>Duration </strong><input class="inputs" id="dur" type="number" placeholder="Enter min Duration"> min

		</div>
		<div class="moviedescription">
			<p style="color:#000"><strong >Description</strong></p>
			<textarea rows="4" style="width:100%" class="inputs" id="descr" type="text" placeholder="Enter Description"> </textarea>
		</div><br>
		<strong>Genres </strong>
		<div class="moviegenres" id="movgenres">

		</div>
		<select class="inputs" id="genresselect">
		<?php
		$genres = array("Action","Adventure","Horror","Short","Drama","Mystery","Comedy","Fantasy","Sci-Fi","Romance","Animation","Crime","Talk-Show","Family","Documentary","Reality-TV","Music","History","Western","Biography","War","Musical","Game-Show","Sport","Film-Noir","Adult");
		$count=1;
		foreach ($genres as &$genre)
		{
			 echo "<option value=\"".$genre.$count."\">".$genre."</option>";
			 $count++;
		}
		?>
		</select>
		<button onclick="loadGenre()" class="sidebutton">Add Genre</button>
		
        <!-- Thumbnail images -->
		<br>
		<strong>Trailers </strong>
        <div class="video-row trailers" id="trailers">
			</div>
			<input class="inputs" type="text" placeholder="Enter Trailer id" id="trailerid">
			<button onclick="loadTrailer()" class="sidebutton">Add Trailer</button>
			<br>
			<strong>Images </strong>
			<div class="movieimages" id="movieimages">
			</div>
			<input class="inputs" type="file" accept="image/x-png,image/gif,image/jpeg" placeholder="Upload Image" id="imageup2">
			<button onclick="uploadImage(2)" class="sidebutton">Add Image</button>
        </div>
		
		<button onclick="collectData()" class="seemore">Save Movie</button>
		</div>
		<script>
        let imgcount = 1;
        let genrecount=1;
        let trailercount=1;
        let genres = document.getElementById("movgenres");
        let trailers = document.getElementById("trailers");
        let images = document.getElementById("movieimages");
		function removeElement(type,el)//Removes element from scene (Used to delete Images, Trailers or Genres from Viewport)
		{
			if(type==1)
			{
				document.getElementById(el).remove();
			}
			else if(type==2)
			{
				trailers.getElementById(el).remove();
			}
			else if (type==3)
			{
				images.getElementById(el).remove();
			}
		}
		function loadGenre()//Loads selected genre
		{
            const genresname = document.getElementById("genresselect").value;
            const genname = genresname.replace(/[0-9]/g, '');
            const genresavailable = genres.children;
            let found = false;
            for (let i=0;i<genresavailable.length;i++)
			{
                const gen = genresavailable[i].name;
                const gen2 = gen.replace(/[0-9]/g, '');
                if (gen2 === genname)
				{
					found = true;
				}
			}
			if(!found)
			{
			genres.innerHTML+="<a id=\"gen"+genrecount+"\" class=\"genrebutton\" name=\""+genresname+"\">"+genname+
				"<span class=\"fa fa-times cancelbutton\" onclick=\"removeElement(1,'gen"+genrecount+"')\" ></span>"+
				"</a>";
			genrecount++;
			}
		}
		function loadTrailer()//Loads trailer using the id user gave
		{
            const trailerid = document.getElementById("trailerid");
            trailers.innerHTML+="<div class=\"metacolumn\" id=\"trailer"+trailercount+"\">"+
					"<img class=\"video-demo active cursor\" src=\"https://img.youtube.com/vi/"+trailerid.value+"/0.jpg\" style=\"width:100%\" alt=\""+document.getElementById("movietitle").value+"\">"+
					"<span class=\"fa fa-times cancelbutton white\" onclick=\"removeElement(1,'trailer"+trailercount+"')\" ></span>"+
					"</div>";
			trailercount++;
			trailerid.value = '';
		}
		function uploadImage(x)//uploads to server the image user loaded
		{
            const property = document.getElementById("imageup" + x).files[0];
            const image_name = property.name;
            const image_extension = image_name.split(".").pop().toLowerCase();
            if(jQuery.inArray(image_extension,['gif','png','jpg','jpeg'])== -1)
			{
				alert("Invalid Image File");
			}
            const image_size = property.size;
            if(image_size >5000000)
			{
				alert("Image File Size is very big");
			}
			else
			{
                const form_data = new FormData();
                form_data.append("file",property);
				$.ajax({
					url:"phpserver.php",
					method:"POST",
					data:form_data,
					contentType:false,
					cache:false,
					processData:false,
					beforeSend:function(){
						//
					},
					success:function(data)
					{
						//
						if (x==1)
						{
							document.getElementById("previewdiv").innerHTML= "<img id=\"previmg\" class=\"video-demo active cursor\" src=\"./Images/"+data+"\" style=\"\" alt=\"previewimage\">";
							$("#imageup1").val(null);
						}
						else if(x==2)
						{
						images.innerHTML+="<div class=\"metacolumn\" id=\"image"+imgcount+"\" > "+
						"<img class=\"video-demo active cursor\" src=\"./Images/"+data+"\" style=\"width:100%\" alt=\""+document.getElementById("movietitle").value+"\">"+
						"<span class=\"fa fa-times cancelbutton white\" onclick=\"removeElement(1,'image"+imgcount+"')\"></span> </div>";
						$("#imageup2").val(null);
						imgcount++;
						}
					}
				})
			}
		}
		function collectData()// Collects all data user submitted
		{
            let check = true;
            let pass = true;
            const date = new Date(document.getElementById("reldate").value);
            const title = document.getElementById("movietitle").value;
            const description = document.getElementById("descr").value;
            const dateready = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
            const year = date.getFullYear();
            const dur = document.getElementById("dur").value;
            if(document.getElementById("previmg")!=null)
			{
				let preview = document.getElementById("previmg").src;
				preview = preview.replace("./Images/","");
			}
			else {alert("Please fill everything"); return;}

            const trailersl = document.getElementById("trailers").children;
            let trailers = "";
            for (let i=0;i<trailersl.length;i++)
			{
                const temp = trailersl[i].children;
                const temp2 = temp[0].src;
                const stripped = temp2.replace('https://img.youtube.com/vi/', '');
                const stripped2 = stripped.replace('/0.jpg', '');
                trailers += stripped2 + ",";
			}
			if (trailers.length>0)
			{
                const temptrailers = trailers.slice(0, -1);
                trailers = temptrailers;
			}else if(check){check=false;}

        const imagesl = document.getElementById("movieimages").children;
            let images = "";
            for (let i=0;i<imagesl.length;i++)
			{
                const temp = imagesl[i].children;
                const temp2 = temp[0].src;
                const stripped = temp2.replace('./Images/', '');

                images += stripped + ",";
			}
			if (images.length>0)
			{
				var tempimages = images.slice(0,-1);
				images = tempimages;
			}else if(check){check=false;}
			else if(!check){pass=false;}

            const genresavailable = genres.children;
            let genreslist = "";
            for (i=0;i<genresavailable.length;i++)
			{
                const gen = genresavailable[i].name;
                const gennum = gen.replace(/\D/g, '');
                genreslist+=gennum+",";
			}
			if (genreslist.length>0)
			{
                const tempgenres = genreslist.slice(0, -1);
                genreslist = tempgenres;
			}else{pass=false;}

			if(pass && dateready && title && description && year && dur)
			{
				
			}else{alert("Please fill everything"); return;}
			console.log("sunexise");
            const type = "add";
            const datas = "{\"type\":\"" + type + "\",\"title\":\"" + title + "\",\"description\":\"" + description + "\",\"year\":\"" + year + "\",\"releasedate\":\"" + dateready + "\",\"duration\":\"" + dur + "\",\"preview\":\"" + preview + "\",\"trailers\":\"" + trailers + "\",\"photos\":\"" + images + "\",\"genres\":\"" + genreslist + "\"}";
            console.log(datas);
            const dat = datas.replace('&', '%26');
            jQuery.ajax({
			url:"updatemovie.php",
			type:"POST",
			data:dat,
			dataType:"json",
			success:function(result)
			{
				console.log(result);
				if(result=='success')
				{
					alert("Movie Added Successfully!");
					document.getElementById("movietitle").value="";
					document.getElementById("previmg").remove();
					document.getElementById("reldate").value="";
					document.getElementById("dur").value="";
					document.getElementById("descr").value="";
					document.getElementById("movgenres").innerHTML="";
					document.getElementById("trailers").innerHTML="";
					document.getElementById("movieimages").innerHTML="";
				}
				else if(result=='error')
				{
					alert("Error! Characters: \", <, > not allowed.");
				}
				
			},
			error: function(){
				alert('Something Went Wrong!');
			}
		});
		}
		</script>
		<div class="footer">
		<footer>
		<div style="text-align: center;">© 2021 Pop Cinemas. All Rights Reserved.</div>
		</footer>
		</div>
		</div>
		</div>

    </body>
</html>
