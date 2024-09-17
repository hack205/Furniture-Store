<x-filament-panels::page>
    <x-filament-panels::form wire:submit="update">
        {{ $this->form }}
        <canvas id="myCanvas" width="500px" height="800px"></canvas>
        <x-filament-panels::form.actions :actions="$this->getFormActions()"/>
    </x-filament-panels::form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const saveButton = document.querySelector('[form="saveButton"]');
            const canvas = document.getElementById('myCanvas');
            const ctx = canvas.getContext('2d');
            const savedData = @json($this->canvasData);
            const gridSize = 50;
            let elements = [];
            let selectedItem = null;
            let offsetX, offsetY;

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

            function isMouseOverItem(x, y, item) {
                const textWidth = ctx.measureText(item.content).width;
                const textHeight = item.fontSize;
                return x >= item.x && x <= item.x + textWidth && y >= item.y - textHeight && y <= item.y;
            }

            function onMouseDown(e) {
                const mouseX = e.offsetX;
                const mouseY = e.offsetY;

                selectedItem = elements.find(item => isMouseOverItem(mouseX, mouseY, item));
                if (selectedItem) {
                    offsetX = mouseX - selectedItem.x;
                    offsetY = mouseY - selectedItem.y;
                }
            }

            function onMouseMove(e) {
                if (selectedItem) {
                    selectedItem.x = e.offsetX - offsetX;
                    selectedItem.y = e.offsetY - offsetY;
                    drawElements();
                }
            }

            function onMouseUp() {
                selectedItem = null;
            }

            function createText(x, y, content) {
                elements.push({
                    type: 'text',
                    x: x,
                    y: y,
                    content: content,
                    color: 'black',
                    fontSize: 20,
                    fontFamily: 'Arial'
                });
                drawElements();
            }

            canvas.addEventListener('mousedown', onMouseDown);
            canvas.addEventListener('mousemove', onMouseMove);
            canvas.addEventListener('mouseup', onMouseUp);

            saveButton.addEventListener('click', function() {
                    //TODO:: update data
                    Livewire.dispatch('updateCanvasData', elements);

            });

            const fields = JSON.parse(savedData)
            fields.forEach(function(field) {
                createText(field.x, field.y, field.content);
            });

            drawElements();
        });
    </script>
</x-filament-panels::page>
