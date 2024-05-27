
const imagesTop = [
    { src: "/wp-content/themes/jannah-child/img/tool-3.png", link: "https://earthhq.org/", text: "Earth HQ" },
    { src: "/wp-content/themes/jannah-child/img/tool-2.png", link: "https://studio.mongabay.com", text: "Data Studio" },
    { src: "/wp-content/themes/jannah-child/img/tool-1.png", link: "https://www.mongabay.org", text: "Mongabay" }
];

const imagesBottom = [
    { src: "/wp-content/themes/jannah-child/img/tool-2.png", link: "https://studio.mongabay.com", text: "Data Studio" },
    { src: "/wp-content/themes/jannah-child/img/tool-1.png", link: "https://www.mongabay.org", text: "Mongabay" },
    { src: "/wp-content/themes/jannah-child/img/tool-3.png", link: "https://earthhq.org/", text: "Earth HQ" }
];

// const imagesTop = widgetData;
// const imagesBottom = widgetData;

const carruselTop = d3.select("#carrusel-top");
const carruselBottom = d3.select("#carrusel-bottom");

carruselTop.selectAll("a")
    .data(imagesTop)
    .enter()
    .append("a")
    .attr("href", d => d.link)
    .attr("target", "_blank")
    .style("position", "relative")
    .each(function(d) {
        d3.select(this).append("span")
            .text(d.text)
            .classed("hover-text", true);
    })
    .append("img")
    .attr("src", d => d.src);

carruselBottom.selectAll("a")
    .data(imagesBottom)
    .enter()
    .append("a")
    .attr("href", d => d.link)
    .attr("target", "_blank")
    .style("position", "relative")
    .each(function(d) {
        d3.select(this).append("span")
            .text(d.text)
            .classed("hover-text", true);
    })
    .append("img")
    .attr("src", d => d.src);

function scrollHandler() {
    const scrollTop = window.scrollY;
    const scrollBottom = scrollTop + window.innerHeight;

    if (carruselTop.node()) {
        const carruselTopRect = carruselTop.node().getBoundingClientRect();
        const carruselTopWidth = carruselTopRect.width;
        const carruselTopScrollWidth = carruselTop.node().scrollWidth;
        
        let scrollXTop = scrollTop % carruselTopScrollWidth;
        if (scrollXTop > carruselTopWidth) {
            scrollXTop -= carruselTopWidth;
        }
        
        carruselTop.style("transform", `translateX(-${scrollXTop}px)`);
    }

    if (carruselBottom.node()) {
        const carruselBottomRect = carruselBottom.node().getBoundingClientRect();
        const carruselBottomWidth = carruselBottomRect.width;
        const carruselBottomScrollWidth = carruselBottom.node().scrollWidth;
        
        let scrollXBottom = scrollTop % carruselBottomScrollWidth;
        if (scrollXBottom > carruselBottomWidth) {
            scrollXBottom -= carruselBottomWidth;
        }
        
        carruselBottom.style("transform", `translateX(${scrollXBottom}px)`);
    }
}

window.addEventListener("scroll", scrollHandler);