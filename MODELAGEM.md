#MODELAGEM

A modelagem desse framework segue os seguintes motivos:

###Porque temos a pasta "Counties" com os municipos e suas classes extendendo do modelo ? Não poderia ser mais simples com apenas configurações tipo JSON ?

Na verdade não, pois os municipios podem e fazem alterações especificas para suas próprias necessidades, então em caso de existirem variações sobre o meodelo, essas variações devem ser colocadas nessas classes especificas. Isso acaba ampliando bastante o numero de classes e de pastas no framwork mas isso é preferível a ter tudo de forma separada o que tornaria mais dificil de manter, já que várias estruturas podem ser compartilhadas entre si.

Além disso uma prefeitura pode e muda de provedor facilmente (para eles) e da forma como foi estruturado basta apontar para  o novo modelo e fazer alguns testes basicos e o serviço estará novamente em funcionamento, desta vez com os novos padrões forneceidos por esse outro modelo.

