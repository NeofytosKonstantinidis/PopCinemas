<link rel="icon" href="./Images/popcornlogo3.png">
<div id="topCurtain" > <img alt="curtains" src="./Images/TopCurtain.png" draggable="false" style="width:100%"/> </div>
        <div id="leftCurtain"> <img alt="curtains" src="./Images/LeftCurtain.png" draggable="false" style="height:100%" /> </div>
        <div id="rightCurtain"> <img alt="curtains" src="./Images/RightCurtain.png" draggable="false" style="height:100%" /> </div>
        <div id="leftAnimatedCurtain"> <img alt="curtains" src="./Images/leftAnimatedCurtain.jpg" draggable="false" style="width:100%; height:100%" /> </div>
        <div id="rightAnimatedCurtain"><img alt="curtains" src="./Images/rightAnimatedCurtain.png" draggable="false" style="width:100%; height:100%" /> </div>
        <div id="begin">
            <div id="banner">
                <div id="logodiv">
                 <img id="logo" onclick="CloseCurtains('./')" src="./Images/popcornlogo3.png" alt="logo">
                </div>
                <?php
                session_start();
                $loggedin=false;
                if(isset($_SESSION['user'],$_SESSION['role']))//Checks if there is a logged user stored in session
                {
                    $loggedin=true;
                }
                if((!$loggedin || $_SESSION['role']!=='admin') && isset($adminpage))//If specific page that called curtains.php has variable $adminpage set to true it checks if the user is logged in as admin too
                {
                    header("Location: ./");
                }
                if(!$loggedin && isset($checkoutpage))
                {
                    header("Location: ./");
                }
                ?>
                <div id="menuitems">
                        <div class="subnav"> 
						<button class="subnavbtn">Movies <i class="fa fa-caret-down"></i></button>
						<div class="subnav-content">
						  <a onclick="CloseCurtains('./movies?released=playing_now')" >New Releases</a>
						  <a onclick="CloseCurtains('./movies?released=coming_soon')" >Coming Soon</a>
						</div>
						</div>
                        <?php
                        if($loggedin && $_SESSION['role']==='user')//If user logged in shows user menu
                        {
                        ?>
						<div class="subnav"> 
						<button class="subnavbtn"><?php echo $_SESSION['user'];?> <i class="fa fa-user"></i> <i class="fa fa-caret-down"></i></button>
						<div class="subnav-content">
						    <a onclick="CloseCurtains('./profile')">Profile</a>
                            <a onclick="logOut()" >Log Out</a>
						</div>
						</div>
                        <?php
                        }
                        if($loggedin && $_SESSION['role']==='admin')//If admin logged in shows admin menu
                        {
						?>
						<div class="subnav"> 
						<button class="subnavbtn"><?php echo $_SESSION['user'];?> <i class="fa fa-user-cog"></i> <i class="fa fa-caret-down"></i></button>
						<div class="subnav-content">
						    <a onclick="CloseCurtains('./profile')">Profile</a>
						    <a onclick="CloseCurtains('./timeline')">Timeline</a>
                            <a onclick="CloseCurtains('./projectionschedule')">Schedule</a>
						    <a onclick="CloseCurtains('./projection-rooms')" >Projection Rooms</a>
                            <a onclick="CloseCurtains('./add-movie')" >Add Movie</a>
                            <a onclick="logOut()" >Log Out</a>
						</div>
						</div>
                        <?php
                        }
                        if(!$loggedin)//If not logged in shows login/signup button
                        {
                        ?>
						<div class="subnav"> 
						<button id="login" onclick="openLogin(); " class="subnavbtn" title="Login / Signup"><i class="fa fa-sign-in-alt"></i> </button>
						</div>
                        <?php
                        }
                        ?>
                </div>
                <div id="mobilemenubutton" class="subnav">
                    <button id="login" onclick="openMobileMenu(); " class="subnavbtn"><i class="fa fa-bars"></i> </button>
                </div>
				<div id="searchbox">
                <div id="searchbar">
                    <input class="searchInput" id="searchinp" type="text" name="" placeholder="Search">
					<button class="searchButton" onclick="searchMovies()">
						<i id="searchfa" class="fa fa-search"></i>
					</button>
                </div>
				</div>
            </div>
			<div id="id01" class="modal">
			<?php include 'loginform.html';
			?>
			</div>
			<div id="id02" class="modal">
			<?php include 'signupform.html';
			?>
			</div>
            <div id="mobilemenu">
                <a href="javascript:void(0)" class="closebtn" onclick="closeMobileMenu()">&times;</a>
                <div class="overlaymobile-content">
                    Movies
                    <a onclick="closeMobileMenu();CloseCurtains('./movies?released=playing_now')" >New Releases</a>
                    <a onclick="closeMobileMenu();CloseCurtains('./movies?released=coming_soon')" >Coming Soon</a>
                    <?php
                    if($loggedin && $_SESSION['role']==='user')//If user logged in shows user menu (Mobile)
                    {
                        ?>
                        <?php echo $_SESSION['user'];?><i class="fa fa-user"></i>
                    <a onclick="closeMobileMenu();CloseCurtains('./profile')">Profile</a>
                    <a onclick="closeMobileMenu();logOut()" >Log Out</a>
                        <?php
                    }
                    if($loggedin && $_SESSION['role']==='admin')//If admin logged in shows admin menu (Mobile)
                    {
                        ?>
                        <?php echo $_SESSION['user'];?><i class="fa fa-user-cog"></i>
                                <a onclick="closeMobileMenu();CloseCurtains('./profile')">Profile</a>
                                <a onclick="closeMobileMenu();CloseCurtains('./timeline')">Timeline</a>
                                <a onclick="closeMobileMenu();CloseCurtains('./projectionschedule')">Schedule</a>
                                <a onclick="closeMobileMenu();CloseCurtains('./projection-rooms')" >Projection Rooms</a>
                                <a onclick="closeMobileMenu();CloseCurtains('./add-movie')" >Add Movie</a>
                                <a onclick="closeMobileMenu();logOut()" >Log Out</a>
                        <?php
                    }
                    if(!$loggedin)//If not logged in shows login/signup button (Mobile)
                    {
                        ?>
                            Account
                            <a id="login" onclick="closeMobileMenu();openLogin();" >Login/Signup <i class="fa fa-sign-in-alt"></i> </a>

                        <?php
                    }
                    ?>
                </div>
            </div>

    <script>
        $(function() {
            OpenCurtains();
        });// When page loads OpenCurtains() is called to open the curtains of page.
        let oncooldown = false;
        function OpenCurtains()//Opens page curtains
        {
			if(!oncooldown)
			{
				oncooldown = true;
				document.getElementById("begin").style.display = "block";
				setTimeout(function(){
                    const leftCurtain = document.getElementById("leftAnimatedCurtain");
                    const rightCurtain = document.getElementById("rightAnimatedCurtain");
					leftCurtain.classList.add("moveleft");
					rightCurtain.classList.add("moveright");
					setTimeout(function(){
                    const lc = document.getElementById("leftCurtain");
                    const rc = document.getElementById("rightCurtain");
					lc.style.zIndex = "-1";
					rc.style.zIndex = "-1";
					oncooldown = false;
					},1500);
				}, 500);
			}
        }
        function CloseCurtains(v)//Closes curtains and redirects page to a new one
        {
			if(!oncooldown){
				oncooldown = true;
                const leftCurtain = document.getElementById("leftAnimatedCurtain");
                const rightCurtain = document.getElementById("rightAnimatedCurtain");
                const lc = document.getElementById("leftCurtain");
                const rc = document.getElementById("rightCurtain");

				lc.style.zIndex = "1107";
				rc.style.zIndex = "1107";
				leftCurtain.classList.remove("moveleft");
				rightCurtain.classList.remove("moveright");
				setTimeout(function(){
					if(v==="here")
					{
						location.reload();
					}
					else
					{
					location.href = v;
					}
					oncooldown = false;
				}, 1500);
			}
        }
		function CloseCurtain()// Closes page curtains
        {
			if(!oncooldown){
				oncooldown = true;
                const leftCurtain = document.getElementById("leftAnimatedCurtain");
                const rightCurtain = document.getElementById("rightAnimatedCurtain");
                const lc = document.getElementById("leftCurtain");
                const rc = document.getElementById("rightCurtain");
				lc.style.zIndex = "1107";
				rc.style.zIndex = "1107";
				leftCurtain.classList.remove("moveleft");
				rightCurtain.classList.remove("moveright");
				setTimeout(function(){
						oncooldown = false;
					}, 1500);
			}
        }
		
		const modal = document.getElementById('id01');
        const modal2 = document.getElementById('id02');

		window.onclick = function(event)// Event when clicking
        {
			closeModals(event);
		}
		function closeModals(event)//close Login form / Sign up form when clicking elsewhere
		{
			if (event.target === modal) {
				if (modal.style.display==="block")
				{
					OpenCurtains();
				}
				modal.style.display='none';
			}
			if (event.target === modal2) {
				if (modal2.style.display==="block")
				{
					OpenCurtains();
				}
				modal2.style.display='none';
			}
		}
		function closeModls(index)//Close Login or Signup form on click of X button
        {
            OpenCurtains();
		    if(index==1){
                modal.style.display='none';
            }else{
                modal2.style.display='none';
            }
        }
		function searchMovies()//gets search input and if its not null redirects to movies page with the search tag by calling CloseCurtains Method
		{
			const searchtag = document.getElementById("searchinp").value;
			if(searchtag)
			{
				console.log(searchtag);
				const search = "./movies?search="+searchtag;
				CloseCurtains(search)
			}
		}
		function openLogin()//Opens login form
		{
			if(!oncooldown)
			{
				CloseCurtain();
				setTimeout(function(){document.getElementById('id01').style.display='block';},1500);
			}
			
		}
		function openMobileMenu()//Opens mobile menu
        {
            document.getElementById("mobilemenu").style.width = "100%";
        }
        function closeMobileMenu()// Closes mobile menu
        {
            document.getElementById("mobilemenu").style.width = "0%";
        }
        function logOut()// Logs out from account
        {
            const data ={'type':'signout'};
            const jsondata = JSON.stringify(data);
            jQuery.ajax({
                url: "loginchecker.php",
                type: "POST",
                data: jsondata,
                dataType: "json",
                success: function (result) {
                    if (result['status'] === 'success') {
                        CloseCurtains("here");
                    } else if (result['status'] === 'error') {
                        alert('failed to log you out. Please try again.');
                    }
                },
                error: function () {
                    alert('Something Went Wrong!');
                }
            });
        }
    </script>

