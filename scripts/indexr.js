window.addEventListener("load", init, false);

var buttons = document.getElementsByTagName("button");

for(var i = 0; i < buttons.length; i++) {
    if (buttons[i].id.substr(0, 11) == "likeButton_") {
        buttons[i].addEventListener("mouseup", LikePost, false);
    }
}