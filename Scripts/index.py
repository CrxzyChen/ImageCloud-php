import getopt
import json
import sys
import os

from CEntertainment import CEntertainment
from ImageCloud import ImageCloud, THUMB_COVER, THUMB_ALL


def read_config():
    with open("/var/www/Scripts/config.json", "r") as config_file:
        config = json.loads(config_file.read())
    return config["mongodb"]["username"], config["mongodb"]["password"], config["mongodb"]["host"], config["mongodb"][
        "port"]


if __name__ == "__main__":
    username, password, host, port = read_config()
    ic = ImageCloud(username, password, host, 27018)
    argv = sys.argv[1:]

    download_sign = False
    immediate_sign = False
    cover_sign = THUMB_ALL
    thumb_id = -1

    try:
        opts, args = getopt.getopt(argv, "ic",
                                   ["download=",
                                    "start=",
                                    "download-all",
                                    "--cover"
                                    "immediate",
                                    "check-download",
                                    "check-file"])
    except getopt.GetoptError:
        sys.exit(2)
    for opt, arg in opts:
        if opt == '--start':
            task_id = arg
            ic.start(task_id)
        elif opt == '--download':
            download_sign = True
            thumb_id = arg
        elif opt in ['--cover', '-c']:
            cover_sign = THUMB_COVER
        elif opt == '--download-all':
            cet = CEntertainment(username, password, host, port)
            ic.download_all(THUMB_COVER)
        elif opt in ["-i", "--immediate"]:
            immediate_sign = True
        elif opt == "--check-download":
            pass
            # cet = CEntertainment(username, password, host, port)
            # image_cloud = ImageCloud(username, password, host, port)
            # while True:
            #     not_downloaded = image_cloud.check_downloader()
            #     if not_downloaded == 0:
            #         break
            #     cet.download_manga_cover(success)
        elif opt == "--check-file":
            cet = CEntertainment(username, password, host, port)
    if download_sign:
        if immediate_sign:
            ic.download(thumb_id, cover_sign)
        else:
            print(ic.download(thumb_id, cover_sign, immediate_sign))
