#!/bin/bash
pct exec $1 -- bash -c 'echo -e "'"$2"'\n'"$2"'" | passwd'
