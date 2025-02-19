function printDocument(filePath) {
    const fileExtension = filePath.split('.').pop().toLowerCase();
    const isPdf = fileExtension === 'pdf';

    const printElement = document.createElement(isPdf ? 'iframe' : 'img');
    printElement.src = filePath;

    document.body.appendChild(printElement);

    printElement.onload = function () {
        if (isPdf) {
            printElement.contentWindow.print();
        } else {
            printElement.print();
        }
    };
}