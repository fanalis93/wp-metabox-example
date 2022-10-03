(function ($) {
  $(document).ready(function () {
    var image_url = $("#jkm_image_url").val();
    if (image_url) {
      $("#image-container").html(`<img src="${image_url}" alt=""/>`);
    }

    var images_url = $("#jkm_images_url").val();
    images_url = images_url ? images_url.split(";") : [];
    for (i in images_url) {
      var _image_url = images_url[i];
      $("#images-container").append(
        `<img style="margin-left: 10px" src='${_image_url}' />`
      );
    }

    $(".jkm_dp").datepicker({
      changeMonth: true,
      changeYear: true,
    });
    $("#upload_image").on("click", function (event) {
      event.preventDefault();

      // If the media frame already exists, reopen it.
      // if (frame) {
      //   frame.open();
      //   return;
      // }

      // Create a new media frame
      frame = wp.media({
        title: "Select or Upload Media",
        button: {
          text: "Use this media",
        },
        multiple: false, // Set to true to allow multiple files to be selected
      });

      // when an image is selected in the media frame
      frame.on("select", function () {
        // Get media attachment details from the frame state
        var attachment = frame.state().get("selection").first().toJSON();

        $("#jkm_image_id").val(attachment.id);
        $("#jkm_image_url").val(attachment.sizes.thumbnail.url);

        // Send the attachment URL to our custom image input field.
        $("#image-container").html(
          `<img src="${attachment.sizes.thumbnail.url}" alt=""/>`
        );
      });

      frame.open();
      return false;
    });

    $("#upload_images").on("click", function (event) {
      event.preventDefault();

      // If the media frame already exists, reopen it.
      // if (gframe) {
      //   gframe.open();
      //   return false;
      // }

      // Create a new media frame
      gframe = wp.media({
        title: "Select or Upload Images",
        button: {
          text: "Use this media",
        },
        multiple: true, // Set to true to allow multiple files to be selected
      });

      // when an image is selected in the media frame
      gframe.on("select", function () {
        var image_ids = [];
        var image_urls = [];
        // Get media attachment details from the frame state
        var attachments = gframe.state().get("selection").toJSON();
        $("#images-container").html("");
        for (i in attachments) {
          var attachment = attachments[i];
          image_ids.push(attachment.id);
          image_urls.push(attachment.sizes.thumbnail.url);
          $("#images-container").append(
            `<img src="${attachment.sizes.thumbnail.url}" />`
          );
        }

        $("#jkm_images_id").val(image_ids.join(";"));
        $("#jkm_images_url").val(image_urls.join(";"));

        console.log(image_ids, image_urls);
      });

      gframe.open();
      return false;
    });
  });
})(jQuery);
