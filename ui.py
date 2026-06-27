import tkinter as tk
from tkinter import ttk

from calculator import CalculatorEngine


class CalculatorApp(tk.Tk):
    def __init__(self):
        super().__init__()
        self.title('Scientific Calculator')
        self.geometry('860x650')
        self.minsize(780, 620)
        self.configure(bg='#07111f')
        self.engine = CalculatorEngine()
        self.expression = ''
        self.last_answer = '0'

        self.display_var = tk.StringVar(value='0')
        self.history_var = tk.StringVar(value='Ready')
        self.memory_var = tk.StringVar(value='M: 0')

        self._build_ui()
        self._bind_keys()

    def _build_ui(self):
        main = ttk.Frame(self, padding=18)
        main.pack(fill='both', expand=True)

        display_frame = tk.Frame(main, bg='#0f172a', bd=1, relief='ridge')
        display_frame.pack(fill='x', pady=(0, 12), ipady=8)

        display = tk.Entry(
            display_frame,
            textvariable=self.display_var,
            font=('Segoe UI', 28, 'bold'),
            justify='right',
            bd=0,
            bg='#0f172a',
            fg='white',
            insertbackground='white',
        )
        display.pack(fill='x', padx=16, pady=(10, 4))

        info_row = tk.Frame(display_frame, bg='#0f172a')
        info_row.pack(fill='x', padx=16, pady=(0, 10))
        tk.Label(info_row, textvariable=self.history_var, bg='#0f172a', fg='#94a3b8', font=('Segoe UI', 11)).pack(side='left')
        tk.Label(info_row, textvariable=self.memory_var, bg='#0f172a', fg='#f59e0b', font=('Segoe UI', 11)).pack(side='right')

        buttons_frame = tk.Frame(main, bg='#07111f')
        buttons_frame.pack(fill='both', expand=True)

        button_specs = [
            ['7', '8', '9', '÷', 'C'],
            ['4', '5', '6', '×', '⌫'],
            ['1', '2', '3', '−', '^'],
            ['0', '.', 'π', 'e', '+'],
            ['sin', 'cos', 'tan', 'log', 'ln'],
            ['sqrt', 'exp', '(', ')', 'x!'],
            ['Ans', '(', ')', '=', 'm+'],
        ]

        for row_idx, row in enumerate(button_specs):
            for col_idx, label in enumerate(row):
                color = '#2563eb' if label in {'sin', 'cos', 'tan', 'log', 'ln', 'sqrt', 'exp'} else '#0f172a'
                fg = 'white'
                if label in {'÷', '×', '−', '+', '^', '=', 'C', '⌫', 'x!'}:
                    color = '#f59e0b'
                    fg = '#111827'
                if label == '=':
                    color = '#10b981'
                    fg = 'white'
                if label in {'Ans', 'm+'}:
                    color = '#7c3aed'
                    fg = 'white'
                btn = tk.Button(
                    buttons_frame,
                    text=label,
                    width=10,
                    height=2,
                    bg=color,
                    fg=fg,
                    font=('Segoe UI', 15, 'bold'),
                    relief='flat',
                    command=lambda value=label: self._on_button(value),
                )
                btn.grid(row=row_idx, column=col_idx, padx=6, pady=6, sticky='nsew')

        for i in range(5):
            buttons_frame.columnconfigure(i, weight=1)
        for i in range(len(button_specs)):
            buttons_frame.rowconfigure(i, weight=1)

    def _bind_keys(self):
        self.bind('<Return>', lambda event: self._evaluate())
        self.bind('<BackSpace>', lambda event: self._delete_last())
        self.bind('<Escape>', lambda event: self._clear())
        self.bind('<Key>', self._handle_key)

    def _handle_key(self, event):
        if event.char.isdigit() or event.char in '+-*/().^':
            self._append_text(event.char)
        elif event.char == '!':
            self._append_text('!')
        elif event.char == 'p':
            self._append_text('pi')
        elif event.char == 'e':
            self._append_text('e')
        elif event.char == 'x':
            self._append_text('x')
        elif event.char == 's':
            self._append_text('sin(')
        elif event.char == 'c':
            self._append_text('cos(')
        elif event.char == 't':
            self._append_text('tan(')

    def _on_button(self, value):
        if value == 'C':
            self._clear()
            return
        if value == '⌫':
            self._delete_last()
            return
        if value == '=':
            self._evaluate()
            return
        if value == 'Ans':
            self._append_text(self.last_answer)
            return
        if value == 'm+':
            self.engine.memory_store(float(self.last_answer))
            self.memory_var.set(f'M: {self.engine.memory_recall()}')
            return
        if value == 'x!':
            self._append_text('!')
            return
        if value == 'π':
            self._append_text('pi')
            return
        if value == 'e':
            self._append_text('e')
            return
        if value == 'sin':
            self._append_text('sin(')
            return
        if value == 'cos':
            self._append_text('cos(')
            return
        if value == 'tan':
            self._append_text('tan(')
            return
        if value == 'log':
            self._append_text('log(')
            return
        if value == 'ln':
            self._append_text('ln(')
            return
        if value == 'sqrt':
            self._append_text('sqrt(')
            return
        if value == 'exp':
            self._append_text('exp(')
            return
        if value == '÷':
            self._append_text('/')
            return
        if value == '×':
            self._append_text('*')
            return
        if value == '−':
            self._append_text('-')
            return
        self._append_text(value)

    def _append_text(self, value):
        self.expression += str(value)
        self._refresh_display()

    def _delete_last(self):
        self.expression = self.expression[:-1]
        self._refresh_display()

    def _clear(self):
        self.expression = ''
        self._refresh_display()

    def _refresh_display(self):
        self.display_var.set(self.expression if self.expression else '0')

    def _evaluate(self):
        if not self.expression.strip():
            return
        try:
            result = self.engine.evaluate(self.expression)
            self.history_var.set(f'{self.expression} = {result}')
            self.last_answer = result
            self.expression = result
            self._refresh_display()
            self.memory_var.set(f'M: {self.engine.memory_recall()}')
        except Exception as exc:
            self.history_var.set(f'Error: {exc}')
            self.expression = ''
            self._refresh_display()
