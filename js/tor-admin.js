(function ($) {
  $(function () {
    // Print today's booking
    $("#print-todays-table").on("click", function () {
      var printContents = document.getElementById("todays-booking").innerHTML;
      var w = window.open();
      w.document.write(printContents);
      w.print();
      w.close();
    });

    // Change date
    $(".tor-booking-date-input").change(function () {
      var dateElement = $(this);
      var date = dateElement.val();
      //date to format DD.MM.YYYY
      var dateArr = date.split("-");
      date = dateArr[2] + "." + dateArr[1] + "." + dateArr[0];

      const data = {
        action: "tor_get_all_today_bookings",
        date: date,
      };
      $.post(ajaxurl, data, function (response) {
        $("#todays-booking h2 span").html(date);

        var table_body = $("#todays-booking table tbody");
        // clear table_body
        table_body.empty();
        response.data.forEach(function (booking, key) {
          var choosed_services = booking.choosed_services;
          choosed_services = choosed_services.replace(/(?:\r\n|\r|\n)/g, '<br>');
          var row = "<tr>";
          row += "<td>" + (key + 1) + "</td>";
          row += "<td>" + booking.day_slot + "</td>";
          row += "<td>" + booking.pacient + "</td>";
          row += "<td>" + booking.email + "</td>";
          row += "<td>" + booking.phone + "</td>";
          row += "<td>" + booking.jmbg + "</td>";
          row += "<td>" + choosed_services + "</td>";
          row += "</tr>";
          table_body.append(row);
        });
        console.log("response", response);
      });
    });
  });
})(jQuery);
