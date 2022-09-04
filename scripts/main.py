import sys
sys.path.insert(0, ".")


# import libs
from os.path import exists
import pandas as pd
import numpy as np

from sql.oltConfig import OltConfig
from sql.ponsAnverage import PonsAverage

# Carregando nome do arquivo.
file_read=f"/var/www/html/storage/app/{sys.argv[1]}"

# Checando se existe o arquivo.
if not exists(file_read):
    print("Error: E01001 - Falha ao tentar localizar o arquivo CSV.")
    exit(0)


# Carregamento do dataset.
datacom = pd.DataFrame(
    pd.read_csv(file_read, low_memory=False),
    columns=["Device ID", "Port", "Serial Number", "Rx Power (dBm)"]
)

print(datacom.head())


# Buscando pelas OLTs
olts_config = OltConfig().get_olt_config()

# Carregando modelo da tabela PonsAverage
md_ponsAverage = PonsAverage()

# Filtrando Olts
for olts in olts_config:

    # Filtrando as PONs.
    # Gerando um range de 1 até a Quantidade de pons +1.
    # De acordo coma configuração cadastrada na OLT_CONFIG.
    for ponid in range(1, olts["PONS"]+1, 1):

        # Separando dataframe.
        # Filtrando apeans os clientes que possuem a porta em acordo com o ID da vez.
        separate01=datacom[datacom["Port"]==f"gpon 1/1/{ponid}"]

        # Filtrando Dataframe
        # Separando as OLTs de acordo com o Name da vez.
        separate02=separate01[separate01["Device ID"]==olts["OLT_NAME"]]
        average=np.mean(separate02["Rx Power (dBm)"])

        if "nan" in str(average):
            average=float(0)

        # try:
        md_ponsAverage.save({
            "ID_OLT": olts["id"],
            "PON": f"gpon 1/1/{ponid}",
            "DBM_AVERAGE": average,
        })
        # except Exception as err:
        #     print(err);
