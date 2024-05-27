function getSel()
{
    var txtarea = document.getElementById("content");
    var start = txtarea.selectionStart;
    var finish = txtarea.selectionEnd;
    return txtarea.value.substring(start, finish);
}

QTags.addButton(
    "parallax_shortcode",
    "ParallaxSlide",
    callback
);

function callback()
{
    var selected_text = getSel();
    QTags.insertContent("[parallax-img imagepath='image_url' id='1' px_title='First Title' title_color='#333333' img_caption='Your image caption']");
}