<x-filament-panels::page>
    <x-filament-panels::form wire:submit.prevent="saveCanvas">
        {{ $this->form }}
        <div>
            <label for="fontSize">{{ __('messages.fontSize' )}}</label>
            <input type="range" id="fontSize" name="fontSize" min="9" max="100" value="20">
            <span id="fontSizeValue">9</span>
        </div>
        <canvas id="myCanvas" width="700" height="529"></canvas>
        <x-filament-panels::form.actions :actions="$this->getFormActions()"/>
    </x-filament-panels::form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const saveButton = document.querySelector('[wire\\:click="saveCanvas"]');   
        const canvas = document.getElementById('myCanvas');
        const ctx = canvas.getContext('2d');
        const savedData = @json($this->canvasData);
        const gridSize = 50;
        let elements = [];
        let selectedItem = null;
        let offsetX, offsetY;
        let currentFontSize = 20;

        function drawGrid() {
            ctx.strokeStyle = '#ccc';
            ctx.lineWidth = 0.5;
            for (let x = 0; x <= canvas.width; x += gridSize) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, canvas.height);
                ctx.stroke();
            }
            for (let y = 0; y <= canvas.height; y += gridSize) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(canvas.width, y);
                ctx.stroke();
            }
        }

        function drawElements() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            drawGrid();
            elements.forEach(element => {
                ctx.fillStyle = element.color;
                ctx.font = `${element.fontSize}px ${element.fontFamily}`;
                ctx.fillText(element.content, element.x, element.y);
            });
        }

        function createText(x, y, content, fontSize = currentFontSize) {
            elements.push({
                type: 'text',
                x: x,
                y: y,
                content: content,
                color: 'black',
                fontSize: fontSize,
                fontFamily: 'Arial'
            });
            drawElements();
        }

        function updateTextFontSize(fontSize) {
            elements.forEach(element => {
                element.fontSize = fontSize;
            });
            drawElements();
        }

        function isMouseOverItem(mouseX, mouseY, item) {
            const textWidth = ctx.measureText(item.content).width;
            const textHeight = parseInt(ctx.font, 10);

            return mouseX >= item.x &&
                   mouseX <= item.x + textWidth &&
                   mouseY >= item.y - textHeight &&
                   mouseY <= item.y;
        }

        if (savedData.length > 0) {
            currentFontSize = savedData[0].fontSize || 20;
            document.getElementById('fontSize').value = currentFontSize;
            document.getElementById('fontSizeValue').textContent = currentFontSize;

            savedData.forEach(field => createText(field.x, field.y, field.content, field.fontSize));
        } else {
            createText(249.16, 142.2, 'No copia', 9);
            createText(588.48, 144.2, 'No original', 9);
            createText(207.9, 166.32, 'A copia', 9);
            createText(540.1, 166.32, 'A original', 9);
            createText(245.7, 166.32, 'De copia', 9);
            createText(586.3, 166.32, 'De original', 9);
            createText(289.5, 166.32, 'Del copia', 9);
            createText(637.12, 166.32, 'Del original', 9);
            createText(453.6, 196.56, 'Nombre copia', 9);
            createText(113.4, 196.56, 'Nombre original', 9);
            createText(453.6, 211.68, 'Dirección copia', 9);
            createText(113.4, 211.68, 'Dirección original', 9);
            createText(452.6, 233.36, 'Entre copia', 9);
            createText(114.4, 233.36, 'Entre original', 9);
            createText(586.6, 233.36, 'Y copia', 9);
            createText(223.4, 236.36, 'Y original', 9);
            createText(453.6, 253.36, 'Colonia copia', 9);
            createText(113.4, 253.36, 'Colonia original', 9);
            createText(453.6, 273.36, 'Ciudad copia', 9);
            createText(113.4, 273.36, 'Ciudad original', 9);
            createText(453.6, 293.36, 'Mercancia copia', 9);
            createText(113.4, 293.36, 'Mercancia original', 9);
            createText(450.6, 364.36, 'Condiciones de pago copia', 9);
            createText(111.4, 367.36, 'Condiciones de pago original', 9);
            createText(616.6, 347.36, 'Total copia', 9);
            createText(299.4, 346.36, 'Total original', 9);
            createText(299, 368.36, 'Anticipo original', 9);
            createText(615.6, 366.36, 'Anticipo copia', 9);
            createText(300, 387.36, 'Saldo original', 9);
            createText(615.6, 382.36, 'Saldo copia', 9);
        }

        canvas.addEventListener('mousedown', function(e) {
            const mouseX = e.offsetX;
            const mouseY = e.offsetY;
            selectedItem = elements.find(item => isMouseOverItem(mouseX, mouseY, item));
            if (selectedItem) {
                offsetX = mouseX - selectedItem.x;
                offsetY = mouseY - selectedItem.y;
            }
        });

        canvas.addEventListener('mousemove', function(e) {
            if (selectedItem) {
                selectedItem.x = e.offsetX - offsetX;
                selectedItem.y = e.offsetY - offsetY;
                drawElements();
            }
        });

        canvas.addEventListener('mouseup', function() {
            selectedItem = null;
        });

        document.getElementById('fontSize').addEventListener('input', function() {
            currentFontSize = parseInt(this.value, 10);
            document.getElementById('fontSizeValue').textContent = currentFontSize;
            updateTextFontSize(currentFontSize);
        });

        saveButton.addEventListener('click', function() {
            @this.set('canvasData', elements);
        });
        drawElements();
    });
    </script>
</x-filament-panels::page>
