all: doc cadrage report slides

report: cadrage
	pdflatex rapport_intermediaire.tex
	pdflatex rapport_intermediaire.tex

slides:
	pdflatex Slides-intermediary.tex
	pdflatex Slides-intermediary.tex

cadrage:
	pdflatex note_de_cadrage.tex
	pdflatex note_de_cadrage.tex

doc:
	pdflatex rapport.tex
	pdflatex rapport.tex

cleanpdf:
	ls | grep pdf | grep -v cahier | xargs rm -f

clean:
	rm -f *.toc *.aux *.log *.out *.nav *.lof *.snm *~
