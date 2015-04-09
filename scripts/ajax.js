function GetRequestObject() {
    var xhr = null;

    if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    } else if (window.ActiveObject) {
        try {
            objRequest = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch(e) {
            try {
                objRequest = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(e) {}
        }
    }
    return xhr;
}

function LikePost(Event) {
    var element = Event.currentTarget;
    var pid = element.id.substr(11);

    var xhr = GetRequestObject();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 1) {
            element.className += " likeButtonLoading";
            element.removeEventListener("mouseup", LikePost, true);
        }
        if (xhr.readyState == 4 && xhr.status == 200) {
            var responseObj = JSON.parse(xhr.responseText);
            if (responseObj[0].userLiked)
            {
                element.className = "likeButtonPressed";
            } else {
                element.className = "likeButton";
            }
            element.innerHTML = "";
            element.innerHTML = responseObj[0].numLikes;
            element.addEventListener("mouseup", LikePost, true);
        }
    }
    xhr.open("POST","likes.php",true);
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    if (element.classList.contains('likeButton')) {
        xhr.send("add=1&pid=" + pid);
    } else {
        xhr.send("remove=1&pid=" + pid);
    }
}

var lastUpdate;
var isUpdating;
var updateTimer;

function init() {
    lastUpdate = Date.now();
    isUpdating = true;
    updateTimer = setInterval(function(){RefreshPosts()}, 10000); //refresh every 10s
}

function AjaxToggle() {
    if (isUpdating) {
        clearInterval(updateTimer)
        document.getElementById("ajaxToggle").innerHTML = "Auto-Update Disabled";
    } else {
        document.getElementById("ajaxToggle").innerHTML = "Auto-Update Enabled";
        updateTimer = setInterval(function(){RefreshPosts()}, 10000); //refresh every 10s
    }
    isUpdating = !isUpdating;
}

function RefreshPosts() {
    var xhr = GetRequestObject();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            lastUpdate = Date.now();
            var responseObj = JSON.parse(xhr.responseText);

            for (var i = responseObj.add.length-1; i >= 0; i--) {
                AddPost(document, responseObj.add[i]);
            }

            var postContainer = document.getElementById("PostContainer");
            for (var i = postContainer.children.length-1; i >= 10; i--) {
                postContainer.removeChild(postContainer.lastElementChild);
            }

            for (var i = 0; i < responseObj.update.length; i++) {
                UpdatePost(document, responseObj.update[i])
            }
        }
    }
    var url = "getPosts.php?updateTime="+Math.floor(lastUpdate/1000);
    xhr.open("GET", url, true);
    xhr.send();
}

function PingServer() {
    var xhr = GetRequestObject();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log(xhr.responseText);
            lastUpdate = new Date();
        }
    }
    var url = "getTime.php?t="+Math.floor(lastUpdate/1000);
    xhr.open("GET", url, true);
    xhr.send();
}

function UpdatePost(doc, post) {
    var likeButton = doc.getElementById("likeButton_"+post.pid);
    likeButton.className = post.userLiked ? "likeButtonPressed" : "likeButton";
    likeButton.innerHTML = post.numLikes;

    var commentIndicator = doc.getElementById("commentIndicator_"+post.pid);
    var commentValue = post.numComments == 1 ? " Comment" : " Comments";
    commentIndicator.innerHTML = post.numComments + commentValue;
}

function AddPost(doc, post) {
    var postContainer = doc.getElementById("PostContainer");

    var postDiv = doc.createElement("div");
    postDiv.className = "post";
    postDiv.id = "post_"+post.pid;
    postContainer.insertBefore(postDiv, postContainer.childNodes[0]);

    var br = doc.createElement("br");

    var postProfileDiv = CreateDiv("postProfile", postDiv);
        var postProfileImg = CreateImg("postProfileImage", "img/default-user.png", "Profile", postProfileDiv);
        var postProfileNameDiv = CreateDiv("postProfileName", postProfileDiv).innerHTML = post.first_name + " " + post.last_name;
        var postProfileInfoDiv = CreateDiv("postProfileInfo", postProfileDiv);
        var postProfileInfoDiv1 = CreateDiv("right", postProfileDiv);
            var likeButtonClass = post.userLiked ? "likeButtonPressed" : "likeButton";
            var likeButton = CreateButton(likeButtonClass, "likeButton_"+post.pid, postProfileInfoDiv1);
                likeButton.innerHTML = post.numLikes;
                likeButton.addEventListener("mouseup", LikePost, false);
        var postProfileInfoDiv2 = CreateDiv("right", postProfileDiv);
            var postDate = CreateSpan("postDate", postProfileInfoDiv2).innerHTML = post.timestamp;
                postProfileInfoDiv2.appendChild(br);
            var commentValue = post.numComments == 1 ? " Comment" : " Comments";
            var postCommentIndicator = CreateLink("commentIndicator", "post.php?pid="+post.pid, postProfileInfoDiv2)
                postCommentIndicator.id = "commentIndicator_"+post.pid;
                postCommentIndicator.innerHTML = post.numComments + commentValue;

    var postAreaDiv = CreateDiv("postArea", postDiv);
        var PostContent = CreateParagraph(post.content, postAreaDiv);
        if (post.image != null) {
            var PostImage = CreateImg("postImage", stripslashes(post.image), "UserPic", postAreaDiv);
        }
}

function CreateDiv(className, parent)
{
    var e = document.createElement("div");
    e.className = className;
    parent.appendChild(e);
    return e;
}

function CreateImg(className, src, alt, parent)
{
    var e = document.createElement("img");
    e.className = className;
    e.src = src;
    e.alt = alt;
    parent.appendChild(e);
    return e;
}

function CreateButton(className, id, parent) {
    var e = document.createElement("button");
    e.className = className;
    e.id = id;
    parent.appendChild(e);
    return e;
}

function CreateSpan(className, parent) {
    var e = document.createElement("span");
    e.className = className;
    parent.appendChild(e);
    return e;
}

function CreateLink(className, href, parent) {
    var e = document.createElement("a");
    e.className = className;
    e.href = href;
    parent.appendChild(e);
    return e;
}

function CreateParagraph(content, parent) {
    var e = document.createElement("p");
    e.innerHTML = content;
    parent.appendChild(e);
    return e;
}

function stripslashes(str) {
    return (str + '')
        .replace(/\\(.?)/g, function(s, n1) {
            switch (n1) {
                case '\\':
                    return '\\';
                case '0':
                    return '\u0000';
                case '':
                    return '';
                default:
                    return n1;
            }
        });
}