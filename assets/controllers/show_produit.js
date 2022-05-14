$(function () {
  var top =
    $(".element-produit").offset().top -
    parseFloat($(".element-produit").css("marginTop").replace(/auto/, 0));
  var footTop =
    $(".footer").offset().top -
    parseFloat($(".footer").css("marginTop").replace(/auto/, 0));

  var maxY = footTop - $(".element-produit").outerHeight();
  console.log(maxY);
  $(window).scroll(function (e) {
    var y = $(this).scrollTop();
    console.log(y);
    if (y < maxY - 50) {
      $(".element-produit").addClass("fixed").removeAttr("style");
    } else {
      $(".element-produit")
        .removeClass("fixed")
        .css({
          position: "absolute",
          top: maxY - top + "px",
        });
    }
  });
});
