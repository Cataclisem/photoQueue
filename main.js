const addBtn = document.getElementById('addBtn');
const boxes = document.getElementById("boxes")

addBtn.addEventListener("click", () => addTextbox());

let counter = 0;



function addTextbox(type = 'input', initialValue = '') {
    counter++;
    const textboxID = `field-${counter}`;
    const timeID = `time-${counter}`;
    const textBoxContainer = document.createElement('div');
    textBoxContainer.className = 'textBoxContainer';
    // create element depending on type
    let field;
    field = document.createElement('input');
    field.type = 'text';

    field.textboxID = textboxID;
    field.name = textboxID;
    field.placeholder = `Textbox ${counter}`;
    field.value = initialValue;

    let time;
    time.document.createElement("input");


    const remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'remove';
    remove.setAttribute('aria-label', `Remove ${id}`);
    remove.textContent = '✖';
    remove.addEventListener('click', () => textBoxContainer.remove());

    textBoxContainer.appendChild(field);
    textBoxContainer.appendChild(remove);
    boxes.appendChild(textBoxContainer);

    field.focus();
};

function removerAndRemember(name = '') {

};