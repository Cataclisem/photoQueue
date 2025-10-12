const addBtn = document.getElementById('addBtn');
const boxes = document.getElementById("boxes") //The div we place the textboxes in

addBtn.addEventListener("click", () => addTextbox());

let counter = 0; //Define counter for textboxes

function addTextbox(type = 'input', initialValue = '') {
    counter++;
    const textboxID = `field-${counter}`;
    const timeID = `time-${counter}`;
    const textBoxContainer = document.createElement('div'); //Create div for textboxes/buttons to be in
    textBoxContainer.className = 'textBoxContainer'; //Set class for CSS ect.

    // create element depending on type
    let field = document.createElement('input');
    // define attributes, p.s. Name is used when sending data to a server
    field.type = 'text';
    field.textboxID = textboxID;
    field.name = textboxID;
    field.placeholder = `Textbox ${counter}`;
    field.value = initialValue;

    // Define the time textbox
    let time = document.createElement('input');
    time.type = 'text';
    time.textboxID = timeID;
    time.name = timeID;
    time.value = "10:52"


    // Add the remove button
    const remove = document.createElement('button');
    remove.type = 'button'; //Specify that it is a button and not a "submit form"
    remove.className = 'remove';
    remove.setAttribute('aria-label', `Remove ${textboxID}`); //Gives it an invisible ID to help e.g. screen readers 
    remove.textContent = '✖';
    remove.addEventListener('click', () => textBoxContainer.remove());

    textBoxContainer.appendChild(field);
    textBoxContainer.appendChild(time)
    textBoxContainer.appendChild(remove);
    boxes.appendChild(textBoxContainer);

    field.focus(); //places curser inside textbox
};