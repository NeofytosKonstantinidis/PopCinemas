<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html lang="en">
    <head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css'>
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.11.2/css/all.css" integrity="sha384-zrnmn8R8KkWl12rAZFt4yKjxplaDaT7/EUkKm7AovijfrQItFWR7O/JJn4DAa/gx" crossorigin="anonymous">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./css/menustyle.css"/>
	<link rel="stylesheet" href="./css/style.css"/>
    <link rel="stylesheet" href="./css/moviestyle.css"/>
	<link rel="stylesheet" href="./css/indexstyle.css"/>
    <link rel="stylesheet" media="only screen and (max-width: 740px)" href="./css/mobilestyle.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
		<?php
		$id=-1;
		if(isset($_GET["id"]))
		{
			$id = $_GET["id"];
			echo "<script>const movieid='".$id."';</script>";
		}else
		{
			header("Location: ./");
		}
		class Moviec {
			private $title;
			private $desc;
			private $reldate;
			private $duration;
			private $trailers;
			private $photos;
			
			public function __construct($title, $desc, $reldate, $duration, $trailers, $photos){
				$this->title = $title;
				$this->desc = $desc;
				$this->reldate = $reldate;
				$this->duration = $duration;
				$this->trailers = $trailers;
				$this->photos = $photos;
			}
			
			public function getTitle(){
				return $this->title;
			}
			public function getDesc(){
				return $this->desc;
			}
			public function getReldate(){
				return $this->reldate;
			}
			public function getDuration(){
				return $this->duration;
			}
			public function getTrailers(){
				return $this->trailers;
			}
			public function getPhotos(){
				return $this->photos;
			}
			
		}
		$movie=null;
		$cinemas = array();
		$userrating=0;
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "cinemadtbs";
		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error); }

		$sql = "SELECT * FROM movies WHERE Movie_ID=".$id;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			
			while($row = $result->fetch_assoc()) {
				$movie = new Moviec($row['Title'],$row['Description'],$row['ReleaseDate'],$row['Duration'],$row['Trailers'],$row['Photos']);
				
			}     } else {   header("Location: ./"); }
		$trailers = explode (",", $movie->getTrailers());
		$trailerscount = count($trailers);
		if ($trailers[0] ==="")
		{
			$trailerscount=0;
		}
		$images = explode (",", $movie->getPhotos());
		$imagescount = count($images);
		if ($images[0] ==="")
		{
			$imagescount=0;
		}
		$metacount = $trailerscount+$imagescount;
		$utubetag = "";
		$sql= "SELECT cinemas.name, cinemas.cinema_ID FROM projection LEFT JOIN halls ON projection.hall_ID= halls.hall_ID LEFT JOIN cinemas ON halls.cinema_ID= cinemas.cinema_ID WHERE movie_ID=".$id." AND projDate>= curdate() AND cinemas.isOpen=1 GROUP BY cinemas.name";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $count=0;
            while($row = $result->fetch_assoc()) {
                $cinemas[$count]['name']=$row['name'];
                $cinemas[$count]['cinemaid']=$row['cinema_ID'];
                $count++;
            }
        }
        ?>
        <title><?php echo $movie->getTitle(); ?></title>
    </head>
	
    <body>
	<?php
	require 'curtains.php';
    if($loggedin)
    {
        $sql ="SELECT rating FROM moviesrating LEFT JOIN user ON moviesrating.user_ID=user.user_ID WHERE movie_ID=".$id." AND user.username='".$_SESSION['user']."' ";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {
                $userrating = $row['rating'];
            }
        }
    }
	?>
		<div class="sections">
		<div class="section">
        <div class="movie">
        <div class="metas-container">

  <!-- Full-width images with number text -->
		<?php
			for ($i=1; $i<($trailerscount+1); $i++)
			{
				echo "<div class=\"mySlides\">";
				echo "<div class=\"numbertext\">".$i." / ".$metacount."</div>";
				echo "<div class=\"video-container\">";
				echo "<iframe class=\"moviemetas trailer\" src=\"https://www.youtube.com/embed/".$trailers[$i-1]."\" frameborder=\"0\" allow=\"accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture\"  allowfullscreen=\"true\" ></iframe>";
				echo "</div>";
				echo "</div>";
			}
			for ($i=1; $i<($imagescount+1); $i++)
			{
				echo "<div class=\"mySlides\">";
				echo "<div class=\"numbertext\">".($i+$trailerscount)." / ".$metacount."</div>";
				echo "<img class=\"moviemetas\" src=\"./Images/".$images[$i-1]."\">";
				echo "</div>";
			}
		?>

        <!-- Next and previous buttons -->
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>


        <!-- Thumbnail images -->
        <div class="video-row">
			<?php
				for ($i=1; $i<($trailerscount+1); $i++)
				{
					echo "<div class=\"video-column\">";
					echo "<img class=\"video-demo cursor\" src=\"https://img.youtube.com/vi/".$trailers[$i-1]."/0.jpg\" style=\"width:100%\" onclick=\"currentSlide(".$i.")\" alt=\"".$movie->getTitle()."\">";
					echo "</div>";
				}
				for ($i=1; $i<($imagescount+1); $i++)
				{
					echo "<div class=\"video-column\">";
					echo "<img class=\"video-demo cursor\" src=\"./Images/".$images[$i-1]."\" style=\"width:100%\" onclick=\"currentSlide(".($i+$trailerscount).")\" alt=\"".$movie->getTitle()."\">";
					echo "</div>";
				}
				
			?>
        </div>
      </div>
        <div class="moviedetails">
            <div class="mtitle">
                <h2><?php echo $movie->getTitle(); ?></h2>
            </div>
            <div class="moviegenres">
			<?php
				$sql="SELECT Gen_ID as id,Title FROM genre INNER JOIN moviegenres ON genre.Gen_ID = moviegenres.genre_ID WHERE moviegenres.movie_ID=".$id.";";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
			
			while($row = $result->fetch_assoc()) {
				echo "<a class=\"moviegenre\" onclick=\"CloseCurtains('./movies?genre=".$row['id']."')\">".$row['Title']."</a>";
			}
			}
			?>
            </div>
            <div class="moviedata">

                <p><strong>Premier: </strong><?php echo date("l d/m/Y",strtotime($movie->getReldate())); ?></p>
                <p><strong>Duration: </strong><?php echo $movie->getDuration(); ?> min</p>
				<div class="gridstarcontainer">
				<div class="starscontainer">
				<p class="rating"><strong>Rating: </strong></p>
				
				<span class="fa fa-star star1"></span>
				<span class="fa fa-star star2"></span>
				<span class="fa fa-star star3"></span>
				<span class="fa fa-star star4"></span>
				<span class="fa fa-star star5"></span>
				<?php
				$sql="SELECT ROUND(AVG(rating),1)as movierating FROM moviesrating WHERE movie_ID =".$id."";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$rating = $row['movierating'];
					$counter=1;
					while($counter<=floor($rating))
					{
						echo "<span class=\"fa fa-star checked star".$counter."\"></span>";
						$counter++;
					}
					$moded = fmod($rating,1);
					if($moded>0)
					{
						if($moded>0.25 && $moded<0.75)
						{
						echo "<span class=\"fa fa-star-half checked star".$counter."\"></span>";
						}
						else if($moded>0.75)
						{
						echo "<span class=\"fa fa-star checked star".$counter."\"></span>";
						}
					}
				}
				}
				$conn->close();
				?>
				</div>
            </div>
            <?php
            if ($loggedin)
            {
                if($_SESSION['role']==='admin')
                {
                    echo '<button class="seemore" onclick="CloseCurtains(\'./edit-movie?id='.$id.'\');">Edit Movie <i class="fa fa-edit"></i></button>';
                }
            }?>
			</div>

        </div>
        </div>
		<div class="moviedescription">
			<p><strong>Description</strong></p>
			<p class="desc" ><?php echo $movie->getDesc(); ?></p>
		</div>
		
		</div>
        <?php if($loggedin)
        {?>
        <div class="section">
            <div class="sectitle">
                <h2>Rate Movie</h2>
            </div>
            <div class="gridstarcontainer starsrater">
                <div class="starscontainer" id="ratestars">
                    <?php
                    for($i=1;$i<=$userrating;$i++)
                    {
                        echo "<span class=\"fa fa-star star".$i." checked \" id=\"rstar".$i."\" onmouseover=\"rateStars(".$i.",0)\" onmouseout=\"rateStars(".$i.",1)\" onclick=\"rateStars(".$i.",2)\"></span>";
                    }
                    for ($i=$userrating+1;$i<=5;$i++)
                    {
                        echo "<span class=\"fa fa-star star".$i."\" id=\"rstar".$i."\" onmouseover=\"rateStars(".$i.",0)\" onmouseout=\"rateStars(".$i.",1)\" onclick=\"rateStars(".$i.",2)\"></span>";
                    }
                    echo '<script>let stars='.$userrating.';</script>';
                    ?>
                    <span id="rateresult" class="fas" style="grid-column: 7;color: green;"></span>
                </div>
            </div>
            <br>
            <button class="seemore" onclick="saveRating()">Save Rating</button>
        </div>


                <?php
                if(count($cinemas)>0)
                {?>
                    <div class="section gapless">
                    <div class="bookingtickets">
                    <div class="sectitle">
                    <h2>Book Tickets</h2>
                    </div>
                    <?php
                    echo "<label for='cinemas'>Select Cinema: </label><select class='bookinputs' onchange='loadcinemaDates()' name='cinemas' id='cinemas'  autocomplete='off'> <option disabled selected value>Select Cinema</option>";
                    for ($i=0, $iMax = count($cinemas); $i< $iMax; $i++)
                    {
                        echo "<option value='".$cinemas[$i]['cinemaid']."'>".$cinemas[$i]['name']."</option>";
                    }
                    echo "</select>";
                    ?>
                    <div id="datepickerdiv">
                    </div>
                        <?php
                        if ($_SESSION['role'] === 'admin') {
                            echo '<button class="seemore" onclick="CloseCurtains(\'./check-reservations?id=' . $id . '\');">Check Reservations <i class="fa fa-book-open"></i></button>';
                        }
                        ?>
                    </div>
                    </div>
                <?php
                }
                ?>

            <?php
        }else{
            ?>
            <div class="section">
                <div class="sectitle">
                    <h2>Rate Movie</h2>
                    <button class="seemore" onclick="openLogin();">Login <i class="fa fa-sign-in-alt"></i></button>
                </div>
            </div>
            <div class="section">
            <div class="sectitle">
                <h2>Book Tickets</h2>
                <button class="seemore" onclick="openLogin();">Login <i class="fa fa-sign-in-alt"></i></button>
            </div>
            </div>
        <?php
        }
        ?>
		<div class="footer">
		<footer>
		<div style="text-align: center;">© 2021 Pop Cinemas. All Rights Reserved.</div>
		</footer>
		</div>
		</div>
		</div>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js'></script>

        <script>
        let adates = {};
        let slideIndex = 1;
        let hall = [];
        let disabledseats =[];
        let cancelledseats = [];
        let addedseats = [];
        let takenseats = [];
        let rowsnum=0;
        let colnum=0;
        let roomdiv;
        let seatlimit = 1;
        let currprojectionid;
        showSlides(slideIndex);

        function plusSlides(n) {
          showSlides(slideIndex += n);
        }

        function currentSlide(n) {
          showSlides(slideIndex = n);
        }

        function showSlides(n) {
            const slides = document.getElementsByClassName("mySlides");
            const dots = document.getElementsByClassName("video-demo");
            if (n > slides.length) {slideIndex = 1}
          if (n < 1) {slideIndex = slides.length}
          for (let i = 0; i < slides.length; i++) {
              slides[i].style.display = "none";
          }
          for (let i = 0; i < dots.length; i++) {
              dots[i].className = dots[i].className.replace(" active", "");
          }
          slides[slideIndex-1].style.display = "block";
          dots[slideIndex-1].className += " active";
        }
		function createDatePicker() {

		  function setdates(date) {
			  const fulldate = adates[date];
			  if (fulldate) {
				return [true];
			  }
			  return [false];
		  }

		  $('#date').datepicker({
			onClose: function(dateText, inst) { 
				$(this).attr("disabled", false);
			},
			beforeShow: function(input, inst) {
			  $(this).attr("disabled", true);
			},
            onSelect: function(dateText){
			    setTimes(dateText);
            },
			beforeShowDay: setdates,
			minDate: 0,
			dateFormat: 'dd/mm/yy',
		  });
		}
        function setTimes(date)
        {
            const cinema = document.getElementById('cinemas');
            const data = {"type":"date","date":date,"movieid":movieid,"cinemaid":cinema.value};
            const jsondata = JSON.stringify(data);
            jqry(jsondata,'date');
        }
        function jqry(jsondata,type)
        {
            jQuery.ajax({
                url:"bookmovie.php",
                type:"POST",
                data:jsondata,
                dataType:"json",
                success:function(result)
                {
                    if(result['status']==='success')
                    {
                        handleResponse(result,type);
                    }
                    else if(result['status']==='error')
                    {
                        alert("Error! Something Went Wrong with the data.");
                    }
                },
                error: function(){
                    alert('Something Went Wrong!');
                }
            });
        }
        function rateStars(rate, type){
            if (type===0)
            {
                for(let i=1; i<=rate;i++)
                {
                    document.getElementById('rstar'+i).classList.add("starshovered");
                }
            }else if (type===1)
            {
                for(let i=1; i<=rate;i++)
                {
                    document.getElementById('rstar'+i).classList.remove("starshovered");
                }
            }else if (type===2)
            {
                for(let i=1; i<=5;i++)
                {
                    document.getElementById('rstar'+i).classList.remove("checked");
                }
                for(let i=1; i<=rate;i++)
                {
                    document.getElementById('rstar'+i).classList.add("checked");
                }
                stars =rate;
            }
        }
        function saveRating(){
            if(stars>0 && stars<=5)
            {
                const data = {'type':'submitrate','rate':stars,'movieid':movieid}
                const jsondata = JSON.stringify(data);
                jqry(jsondata,'submitrate');
            }
        }
        function loadcinemaDates(){
            const cinema = document.getElementById('cinemas');
            if(cinema.value !=null)
            {
                const data = {"type":"cinema","movieid":movieid,"cinemaid":cinema.value};
                const jsondata = JSON.stringify(data);
                jqry(jsondata,'cinema');
            }
        }
        function loadRoom(){
            const projection = document.getElementById('timessel');
            if(projection.value!=null)
            {
                currprojectionid = projection.value
                const data = {"type":"room","projection_id":currprojectionid};
                const jsondata = JSON.stringify(data);
                jqry(jsondata,'room');
            }
        }
        function handleResponse(result,type)
        {
            if(type==='cinema' && result!=null)
            {
                for(let i=0;i<result['date'].length;i++)
                {
                    const newdate = new Date(result['date'][i]+' 00:00:00');
                    adates[newdate] = newdate;
                }
                const datepickerdiv = document.getElementById('datepickerdiv');
                if(datepickerdiv!=null)
                {
                    datepickerdiv.innerHTML='<label for="date">Select Date:</label> <input type="text" name="date" id="date" class="bookinputs" placeholder="Select Date"> <div id="dateresults"> </div>';
                    createDatePicker();
                }
            }
            else if(type==='date' && result['time']!=null)
            {
                const datesdiv = document.getElementById("dateresults");
                const timearr = result['time'];
                let htmlstring ='<label for="times">Select Projection: </label><select name="times" onchange="loadRoom()" class="bookinputs" id="timessel"><option disabled selected value>Select Projection</option>'
                for(let i=0;i<timearr.length;i++)
                {
                    const tmstmp = timearr[i]['timestamp'];
                    if((timearr[i]['seatsLeft']/timearr[i]['avSeats'])>.5)
                    {
                        htmlstring+='<option class="greend" value="'+timearr[i]['proj_ID']+'">'+tmstmp.substring(0,tmstmp.length-3)+' ('+timearr[i]['hallName']+')</option>'
                    }
                    else if((timearr[i]['seatsLeft']/timearr[i]['avSeats'])>.25)
                    {
                        htmlstring+='<option class="yellowd" value="'+timearr[i]['proj_ID']+'">'+tmstmp.substring(0,tmstmp.length-3)+' ('+timearr[i]['hallName']+')</option>'
                    }
                    else if(timearr[i]['seatsLeft']===0)
                    {
                        htmlstring+='<option class="redd" value="nothing" disabled>'+tmstmp.substring(0,tmstmp.length-3)+' ('+timearr[i]['hallName']+')</option>'
                    }

                }
                htmlstring +='</select><div id="roomdiv"></div>'
                datesdiv.innerHTML = htmlstring;
            }
            else if(type==='room' && result['hallinfo']!=null && result['disabledseats'] != null && result['cancelledseats'] != null && result['takenseats'] != null)
            {
                hall = result['hallinfo'];
                disabledseats =result['disabledseats'];
                cancelledseats = result['cancelledseats'];
                takenseats = result['takenseats'];
                rowsnum=hall['rows'];
                colnum=hall['cols'];
                roomdiv =document.getElementById('roomdiv');
                loadTable();
            }
            else if(type==='submitrate')
            {
                document.getElementById('rateresult').classList.add('fa-check')
            }
        }
        function loadTable(){
            let htmlstring = '<div class="roomtabl"><table class="datastable" align="center" cellpadding="0" cellspacing="10">';
            for(let i=0; i<=rowsnum; i++)
            {
                htmlstring+='<tr>';
                if(i>0)
                {
                    htmlstring +='<td class="roomtd">'+String.fromCharCode(64+i)+'</td>';
                }
                for (let j=0; j<=colnum; j++)
                {
                    if(i===0)
                    {
                        if(j===0)
                        {
                            htmlstring+='<td class="roomtd"></td>';
                        }
                        else
                        {
                            htmlstring+='<td class="roomtd">'+j+'</td>';
                        }
                    }
                    if(i>0 && j>0)
                    {
                        htmlstring+='<td class="roomtd">';
                        htmlstring+='<div><i class="icon-Seat seat" id="seat'+String.fromCharCode(64+i)+j+'" onclick="changeColor(\'seat'+String.fromCharCode(64+i)+j+'\')"></i></div>'+
                            '</td>';
                    }
                }
                htmlstring+='</tr>';
            }
            htmlstring+='</table><h2>Screen</h2></div>';
            htmlstring+='<div class="seatpreviews"> <label for="tickets">Tickets:</label><input type="number" id="tickets" onchange="changeSeatLimit();" class="inputs ticketinp" name="tickets" value="1" min="1" max="'+(hall['avseats']-takenseats.length)+'"><br>'
                +'<div class="prevsdi"><span class="svgicon"><i class="icon-Seat seat previewseat"></i></span> <span class="svgtext">Available Seats</span></div>';
            htmlstring+='<div class="prevsdi"> <span class="svgicon"><i class="icon-Seat seat cancelled previewseat"></i></span><span class="svgtext">Disabled Seats</span></div>';
            htmlstring+='<div class="prevsdi"> <span class="svgicon"><i class="icon-Seat seat taken previewseat"></i></span>'+
                '<span class="svgtext">Taken Seats</span></div>';
            htmlstring+='<div class="prevsdi"> <span class="svgicon"><i class="icon-Seat seat sel previewseat"></i></span>'+
                '<span class="svgtext">Selected Seats</span></div><div id="ticketpreviews"></div><div id="ProceedToCheckOut"></div></div>';
            roomdiv.innerHTML = htmlstring;
            loadSeatsType();
        }
        function loadSeatsType()
        {
            for(let i=0; i<cancelledseats.length; i++)
            {
                let tempseat = document.getElementById(cancelledseats[i]['seat']);
                tempseat.classList.add('cancelled');
            }
            for(let i=0; i<disabledseats.length; i++)
            {
                let tempseat = document.getElementById(disabledseats[i]['seat']);
                tempseat.classList.add('corridorrun');
            }
            for(let i=0; i<takenseats.length; i++)
            {
                let tempseat = document.getElementById(takenseats[i]['seat']);
                tempseat.classList.add('taken');
            }
        }
        function changeSeatLimit()
        {
            seatlimit = document.getElementById('tickets').value;
            if(seatlimit<addedseats.length)
            {
                const num = addedseats.length - seatlimit;
                console.log(num );
                for(let i=0; i<num; i++)
                {
                    document.getElementById(addedseats[addedseats.length-1]['seat']).classList.remove("sel");
                    addedseats.splice(addedseats.length-1,1);
                }
                document.getElementById("ticketpreviews").innerHTML="";
                for(let i=0;i<addedseats.length;i++)
                {
                    addTicketPreview(addedseats[i]['seat'],i+1);
                }
                console.log(addedseats);
            }
        }
        function changeColor(s)
        {
            const seat = document.getElementById(s);
            let success = true;
            if(seat!=null)
            {
                for(let i=0; i<cancelledseats.length; i++)
                {
                    if(cancelledseats[i]['seat']===s)
                    {
                        success=false;
                    }
                }
                for(let i=0; i<disabledseats.length; i++)
                {
                    if(disabledseats[i]['seat']===s)
                    {
                        success=false;
                    }
                }
                for(let i=0; i<takenseats.length; i++)
                {
                    if(takenseats[i]['seat']===s)
                    {
                        success=false;
                    }
                }
                if (success)
                {
                    let add = true;
                    for(let i=0; i<addedseats.length;i++)
                    {
                        if(addedseats[i]['seat']===s)
                        {
                            add = false;
                            addedseats.splice(i,1);
                            seat.classList.remove("sel");
                            removeTicketPreview(s);
                        }
                    }
                    if(add)
                    {
                        if(addedseats.length<seatlimit)
                        {
                            if(addedseats.length===0)
                            {
                                document.getElementById('ProceedToCheckOut').innerHTML='<form id="ticksform" method="post" action="checkout.php" ><input id="hiddenarr" name="arrayres" type="hidden" value="" /> </form> <button  onclick="submitData();" class="seemore" style="border-radius: 20px;">Proceed To Checkout</button>';
                            }
                            addedseats[addedseats.length]={'seat':s};
                            seat.classList.add("sel");
                            addTicketPreview(s,addedseats.length);
                        }
                    }
                }
            }
            console.log(addedseats);
        }
        function addTicketPreview(s,leng)
        {
            const ticketspreviewsdiv = document.getElementById("ticketpreviews");
            const str = s.replace('seat', '');
            const row = str.substring(0,1);
            const column = str.substring(1,str.length);
            if(ticketspreviewsdiv!=null)
            {
                ticketspreviewsdiv.innerHTML += '<div class="prevticket" id="prevticket'+s+'">'
                    +'<div class="titleticket">Selected Seat #'+leng+'</div>'
                    +'<label for="row'+s+'">Ticket Row: </label><input name="row'+s+'" class="smallinput" value="'+row+'" readonly><br> '
                    +'<label for="column'+s+'">Ticket Column: </label><input name="column'+s+'" class="smallinput" value="'+column+'" readonly><br>'
                    +'<label for="cost'+s+'">Cost: </label><input name="cost'+s+'" class="smallinput" value="7,50€" readonly></div>';
            }
        }
        function removeTicketPreview(s)
        {
            document.getElementById('prevticket'+s).remove();
        }
        function submitData()
        {
            CloseCurtain();
            const data = {'seats':addedseats,'projection':currprojectionid}
            document.getElementById('hiddenarr').value = JSON.stringify(data);
            setTimeout(function(){
                document.getElementById('ticksform').submit();
            }, 1500);

        }
        </script>
    </body>
</html>
