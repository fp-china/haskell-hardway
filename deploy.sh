
rsync -avzl --delete \
  --exclude .git/ \
  --stats --progress ./ tiye:/home/chen/repo/haskell-hardway/