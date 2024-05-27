function getSel()
{
    var txtarea = document.getElementById("content");
    var start = txtarea.selectionStart;
    var finish = txtarea.selectionEnd;
    return txtarea.value.substring(start, finish);
}

QTags.addButton(
    "pxcontent_shortcode",
    "ParallaxContent",
    callback
);

function callback()
{
    var selected_text = getSel();
    QTags.insertContent("[open-parallax-content]" +  selected_text + "[close-parallax-content]")
}