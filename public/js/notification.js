function createRandomMessage(title, type) {

    return title;
}

function newNotification(title, type) {

    switch (type) {
        case 0:
            createNotification(
                createRandomMessage(title, type),
                NotificationType.SUCCESS
            );
            break;
        case 1:
            createNotification(
                createRandomMessage(title, type),
                NotificationType.INFO
            );
            break;
        case 2:
            createNotification(
                createRandomMessage(title, type),
                NotificationType.WARNING
            );
            break;
        case 3:
            createNotification(
                createRandomMessage(title, type),
                NotificationType.CRITICAL
            );
            break;
    }
}


class NotificationType {
    // Create new instances of the same class as static attributes
    static SUCCESS = new NotificationType(0, "Success", "notification-success");
    static INFO = new NotificationType(1, "Information", "notification-info");
    static WARNING = new NotificationType(2, "Warning", "notification-warning");
    static CRITICAL = new NotificationType(3, "ERROR", "notification-critical");

    constructor(name, title, notificationClass) {
        this.name = name;
        this.title = title;
        this.notificationClass = notificationClass;
    }
}

function createNotification(message, type) {
    const title = type.title;
    const notificationClass = type.notificationClass;
    const notificationId = new Date().getTime();

    var notification = document.createElement("DIV");
    notification.classList.add("notification");
    notification.classList.add(notificationClass);
    notification.id = notificationId;
    notification.onmouseenter = function (event) {
        document.getElementById(notificationId).setAttribute("created-at", "-");
    };
    notification.onmouseleave = function (event) {
        document
            .getElementById(notificationId)
            .setAttribute("created-at", new Date().getTime());
    };

    var notificationLoader = document.createElement("DIV");
    notificationLoader.classList.add("notification-loader");

    var closeButton = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "svg"
    );
    closeButton.classList.add("close-button");
    closeButton.width = "15px";
    closeButton.height = "15px";
    closeButton.onclick = function (event) {
        document.getElementById(notificationId).remove();
    };

    var closeButtonLineA = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "line"
    );
    closeButtonLineA.setAttribute("x1", "3");
    closeButtonLineA.setAttribute("y1", "3");
    closeButtonLineA.setAttribute("x2", "12");
    closeButtonLineA.setAttribute("y2", "12");
    closeButton.appendChild(closeButtonLineA);
    var closeButtonLineB = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "line"
    );
    closeButtonLineB.setAttribute("x1", "3");
    closeButtonLineB.setAttribute("y1", "12");
    closeButtonLineB.setAttribute("x2", "12");
    closeButtonLineB.setAttribute("y2", "3");
    closeButton.appendChild(closeButtonLineB);

    var notificationTitle = document.createElement("SPAN");
    notificationTitle.classList.add("notification-title");
    notificationTitle.innerText = title;

    var notificationDescription = document.createElement("P");
    notificationDescription.classList.add("notification-text");
    notificationDescription.innerHTML = message;

    notification.appendChild(notificationLoader);
    notification.appendChild(closeButton);
    notification.appendChild(notificationTitle);
    notification.appendChild(notificationDescription);
    notification.setAttribute("created-at", new Date().getTime());
    document.getElementById("notifications").appendChild(notification);
    /*
  setTimeout(() => {
    const notification = document.getElementById(notificationId);
    notification?.remove();
  }, 10000); */
}

function checkNotifications() {
    const notifications = document.getElementsByClassName("notification");
    for (let i = 0; i < notifications.length; i++) {
        if (
            (new Date().getTime() - notifications[i].getAttribute("created-at")) / 1000 >
            10
        ) {
            notifications[i].remove();
        }
    }
}

setInterval(function () {
    checkNotifications();
}, 100);
